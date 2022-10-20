<?php

namespace App\Controllers;

use App\Models\User;
use Core\Model;
use Core\View;

class Update extends Authenticated
{
    public function indexAction()
    {
        $id = $_GET['id'];
        $user = User::findByID($id);
        View::renderTemplate('Update/index.twig', ['user' => $user]);
    }
    public function update()
    {
        $user = new User($_POST);
        if ($user->update()){
            $this->redirect('/users');
        } else {
            View::renderTemplate('/update/index.twig', ['user' => $user ]);
        }
    }
}
