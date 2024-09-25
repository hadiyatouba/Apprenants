<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\AuthenticationInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthenticationInterface $authService)
    {
        $this->authService = $authService;
        
    }

    public function login(Request $request)
    {
         $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $this->authService->login($credentials);

            return response()->json([

                'user' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'login' => $user->login,
                    'token' => $token,
                ]
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users',
            'telephone' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'role_id' => 'required|integer|exists:roles,id',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'statut' => 'nullable|string|in:ACTIF,INACTIF,SUPPRIME',
            'photo' => 'required|file|image|max:2048',
        ]);

        return $this->authService->register($data);
    }

    public function logout()
    {
        return $this->authService->logout();
    }

    public function refresh()
    {
        return $this->authService->refreshToken();
    }
}
