<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\FirebaseStorageService;
use App\Services\FirebaseServices; // Ensure you import this

class UserController extends Controller
{
    protected $firebaseServices;
    protected $firebaseStorage;

    public function __construct(FirebaseServices $firebaseServices, FirebaseStorageService $firebaseStorageService)
    {
        $this->firebaseServices = $firebaseServices; // Now it knows how to resolve this
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

        // Validate the user data
        $validatedData = $this->validateUserData($request);

        // Check if a photo is provided
        if ($request->hasFile('photo')) {
            $photoPath = $this->firebaseStorage->uploadFile($request->file('photo'), 'users/' . $validatedData['login'] . '/photo');
            $validatedData['photo'] = $photoPath; // Save photo link in PostgreSQL
        } else {
            return response()->json(['error' => 'La photo est requise'], 422);
        }

        // Create the user in PostgreSQL
        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        // Create the user in Firebase Authentication and Realtime Database
        $firebaseUserData = [
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'login' => $validatedData['login'],
            'email' => $validatedData['email'],
            'telephone' => $validatedData['telephone'],
            'fonction' => $validatedData['fonction'],
            'statut' => $validatedData['statut'],
            'role_id' => $validatedData['role_id'],
            'photo' => $validatedData['photo'], // Include photo URL in Firebase
        ];

        try {
            $firebaseUid = $this->firebaseServices->createUser($validatedData['email'], $request->password, $firebaseUserData);
            Log::info("Utilisateur créé dans Firebase avec UID: " . $firebaseUid);
        } catch (\Exception $e) {
            // If the Firebase insertion fails, delete the user from PostgreSQL
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