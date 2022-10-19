<?php

namespace App\Controllers;

use App\Auth;
use App\Flash;
use Core\View;
use \App\Models\User;

class Login extends \Core\Controller
{
    public function newAction(){
        View::renderTemplate('Login/new.twig');
    }

    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        if ($user) {
           Auth::login($user);
           Flash::addMessage('Login successful');
           $this->redirect(Auth::getReturnToPage());
        } else {
            Flash::addMessage('Login unsuccessful, please try again', Flash::WARNING);
            View::renderTemplate('Login/new.twig', ['email' => $_POST['email']]);
        }
    }

    public function  destroyAction()
    {
        Auth::logout();
        $this->redirect('/login/show-logout-message');
    }

    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout successful');
        $this->redirect('/');
    }

}