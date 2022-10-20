<?php

namespace App\Controllers;

use Core\View;
use App\Models\User;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.twig');
    }

    public function createAction()
    {
        $user = new User($_POST);
        if ($user->save()){
            $this->redirect('/signup/success');
        } else {
            View::renderTemplate('signup/new.twig', ['user' => $user]);
        }
    }
    public function successAction()
    {
        echo 'Success create, please log in';
        View::renderTemplate('/login/new.twig');
    }
}
