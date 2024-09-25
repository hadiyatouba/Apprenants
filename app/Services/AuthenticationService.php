<?php

namespace App\Services;

use App\Models\User;
use App\Services\FirebaseServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\FirebaseStorageService;
use App\Interfaces\AuthenticationInterface;
use App\Models\Role;

class AuthenticationService implements AuthenticationInterface
{
    protected $firebaseServices;
    protected $firebaseStorage;

    public function __construct(FirebaseStorageService $firebaseStorage)
    {
        $this->firebaseServices = new FirebaseServices();
        $this->firebaseStorage = $firebaseStorage;
    }



    public function register(array $data)
    {
        // Upload la photo dans Firebase Storage
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $path = 'photos/' . uniqid() . '.' . $data['photo']->getClientOriginalExtension();
            $photoUrl = $this->firebaseStorage->uploadFile($data['photo'], $path);
            $data['photo'] = $photoUrl;
        } else {
            $data['photo'] = null;
        }
        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'photo' => $data['photo'] ?? null,
            'login' => $data['login'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'password' => Hash::make($data['password']),
            'fonction' => $data['fonction'],
            'statut' => $data['statut'] ?? 'ACTIF',
            'role_id' => $data['role_id'],
        ]);

       // Créer l'utilisateur dans Firebase
    $firebaseUid = $this->firebaseServices->createUser($data['email'], $data['password'], $user->toArray());
    $user->firebase_uid = $firebaseUid;
    $user->save();

    // Créer le token avec Passport
    $token = $user->createToken('AuthToken')->accessToken;

    return response()->json([
        'user' => $user->only([
            'nom',
            'prenom',
            'photo',
            'login',
            'email',
            'telephone',
            'fonction',
            'statut',
            'role_id',
            'id',
            'firebase_uid'
        ]),
        'access_token' => $token,
    ], 201);
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        /** @var User $user */
        $token = $user->createToken('AuthToken');

        return response()->json([
            'access_token' => $token
        ]);
    }


    public function logout()
    {
        $user = Auth::user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refreshToken()
    {
        $user = Auth::user();
        /** @var App\Models\User $user */
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        $newToken = $user->createToken('AuthToken')->accessToken;

        return response()->json(['access_token' => $newToken]);
    }
}
