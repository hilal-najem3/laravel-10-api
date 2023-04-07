<?php

namespace App\Containers\Auth\Helpers;

use App\Containers\Auth\Helpers\UserTokenHelper;
use Illuminate\Support\Facades\Log;
use App\Containers\Users\Helpers\UserHelper;
use App\Models\User;

class UserAuthHelper
{
    /**
     * Login a user using credentials provided
     * credentials are email an password
     * 
     * @param $creds ['email' => 'email@example.com', 'password' => 'password']
     * @return [ 'user' => auth()->user(), 'token' => $token ] | null
     */
    public static function login($creds)
    {
        self::restoreIfDeleted($creds['email']);
        if (auth()->attempt($creds)) {
            Log::info('Login successful');

            $user = UserHelper::email($creds['email']);
            if($user->active) {
                // This user can't login
                $token = UserTokenHelper::create_token(auth()->user());
                return [
                    'user' => UserHelper::profile(),
                    'token' => $token
                ] ;
            } else {
                UserTokenHelper::revoke_all($user); // Revoke all user tokens just in case there are some active ones
            }
        }
        
        Log::info('Login failed');
        return null;
    }

    /**
     * Logout authenticated user
     * 
     * @return bool
     */
    public static function logout($user)
    {
        return UserTokenHelper::revoke_token_for_user(auth()->user()->token(), $user);
    }

    public static function logoutFromAllAndRefreshToken(User $user = null)
    {
        if($user == null) {
            $user = auth()->user();
        }

        UserTokenHelper::revoke_all($user);
        $token = UserTokenHelper::create_token($user);
        return $token;
    }

    /**
     * This function restores a user profile if he was deleted
     * A cron job is responsible for force deleting the users that have not logged in with 60 days
     * 
     * @param string $email
     * @return void
     */
    private static function restoreIfDeleted(string $email): void
    {
        $user = User::onlyTrashed('email', $email)->first();

        if($user != null && $user->can_restore) {
            UserHelper::restoreUser($user);
        }
    }
}