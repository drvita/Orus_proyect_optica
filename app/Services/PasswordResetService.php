<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Generate a 16-character alphanumeric token and save it to the user.
     *
     * @param User $user
     * @return string
     */
    public function generateToken(User $user, bool $isPublic = false): string
    {
        $token = Str::random($isPublic ? 64 : 16);
        $user->remember_token = $token;
        $user->save();

        return $token;
    }

    /**
     * Validate the token and return the associated user.
     *
     * @param string $token
     * @param string $email
     * @return User|null
     */
    public function validateToken(string $token, string $email): ?User
    {
        if (empty($token) || empty($email)) {
            return null;
        }

        return User::where('remember_token', $token)
            ->where('email', $email)
            ->first();
    }
}
