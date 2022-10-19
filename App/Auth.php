<?php

namespace App;

use App\Models\User;

class Auth
{
    public static function login($user)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
    }

    public static function logout(){
        $_SESSION = [];

        if (ini_get('session.use_cookies')){
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }



    public static function rememberRequestPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }
    public static function getReturnToPage()
    {
        return isset($_SESSION['return_to']) ? $_SESSION['return_to'] : '/';
    }
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])){
            return User::findByID($_SESSION['user_id']);
        }
    }

}
