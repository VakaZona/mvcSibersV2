<?php

namespace App\Controllers;

use Core\View;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.twig');
    }

    public function createAction()
    {
        $user = new User($_POST);
        $user->save();
    }
}
