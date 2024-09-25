<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\FirebaseStorageService;

class UserController extends Controller
{
    protected $firebaseService;
    protected $firebaseStorage;
    public function __construct(FirebaseService $firebaseService, FirebaseStorageService $firebaseStorageService)
    {
        $this->firebaseService = $firebaseService;
        $this->firebaseStorage = $firebaseStorageService;
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Filtrer par rôle si spécifié
        if ($request->has('role_id')) {
            $query->ofRole($request->role_id);
        }

        // Filtrer par statut si spécifié
        if ($request->has('status')) {
            $query->withStatus($request->status);
        }

        // Ajouter d'autres logiques de filtrage ici si nécessaire

        $users = $query->paginate(15);
        return response()->json($users);
    }
    public function store(Request $request)
    {
        Log::info('Tentative de création d\'utilisateur', ['user' => auth()->user(), 'requested_role' => $request->role_id]);

        // Valider les données de l'utilisateur
        $validatedData = $this->validateUserData($request);

        // Vérifier si une photo est fournie
        if ($request->hasFile('photo')) {
            $photoPath = $this->firebaseStorage->uploadFile($request->file('photo'), 'users/' . $validatedData['login'] . '/photo');
            $validatedData['photo'] = $photoPath; // Enregistrer le lien de la photo dans PostgreSQL
        } else {
            return response()->json(['error' => 'La photo est requise'], 422);
        }

        // Créer l'utilisateur dans PostgreSQL
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        // Créer l'utilisateur dans Firebase Authentication et Realtime Database
        $firebaseUserData = [
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'login' => $validatedData['login'],
            'password' => $validatedData['password'],
            'email' => $validatedData['email'],
            'telephone' => $validatedData['telephone'],
            'fonction' => $validatedData['fonction'],
            'email' => $validatedData['email'],
            'telephone' => $validatedData['telephone'],
            'fonction' => $validatedData['fonction'],
            'statut' => $validatedData['statut'],
            'role_id' => $validatedData['role_id'],
            'photo' => $validatedData['photo'], // Inclure l'URL de la photo dans Firebase
        ];

        try {
            $firebaseUid = $this->firebaseService->createUser($validatedData['email'], $request->password, $firebaseUserData);
            Log::info("Utilisateur créé dans Firebase avec UID: " . $firebaseUid);
        } catch (\Exception $e) {
            // Si l'insertion échoue dans Firebase, supprimer l'utilisateur de PostgreSQL
            $user->delete();
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur dans Firebase: ' . $e->getMessage()], 500);
        }

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user);
    }
    public function update(Request $request, User $user)
    {
        // Ensure the user is authorized to update this user
        $this->authorize('update', $user);
    
        // Validate only the email, login, and password fields
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'login' => 'required|string|max:255|unique:users,login,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Password is optional, but must be confirmed if provided
        ]);
    
        // Hash the password if it is provided
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            // Remove password from the data if it's not being updated
            unset($validatedData['password']);
        }
    
        // Update the user with validated data
        $user->update($validatedData);
    
        // Return the updated user data as a JSON response
        return response()->json($user);
    }
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->json(null, 204);
    }
    private function validateUserData(Request $request, $userId = null)
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'photo' => 'required|image|max:1024',
            'login' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->when($userId, function ($query) use ($userId) {
                    return $query->whereNot('id', $userId);
                }),
            ],
            'telephone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->when($userId, function ($query) use ($userId) {
                    return $query->whereNot('id', $userId);
                }),
            ],
            'fonction' => 'required|string|max:255',
            'statut' => 'required|string|in:ACTIF,INACTIF',
            'adresse' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->when($userId, function ($query) use ($userId) {
                    return $query->whereNot('id', $userId);
                }),
            ],
            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => 'required|exists:roles,id',
        ];
        return $request->validate($rules);
    }
}
