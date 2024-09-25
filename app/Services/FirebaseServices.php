<?php

namespace App\Services;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseServices
{
    protected $auth;
    protected $database;

    public function __construct()
    {
        $this->auth = Firebase::auth();
        $this->database = Firebase::database();
    }
    

    public function createUser($email, $password, array $firebaseUserData)
    {
        try {
            // Create a new user in Firebase Authentication
            $userProperties = [
                'email' => $email,
                'password' => $password,
                'emailVerified' => false,
            ];

            $createdUser = $this->auth->createUser($userProperties);

            // Save additional user data in Firebase Realtime Database
            $this->database->getReference('users/' . $createdUser->uid)->set($firebaseUserData);

            return $createdUser->uid; // Return Firebase UID
        } catch (\Exception $e) {
            Log::error('Error creating Firebase user: ' . $e->getMessage());
            throw $e;
        }
    }
}
