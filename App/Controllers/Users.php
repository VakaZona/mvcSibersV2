<?php

namespace App\Controllers;

use App\Models;
use Core\View;

class Users extends Authenticated
{
    public function indexAction()
    {
        $users = Models\User::getAll();
        View::renderTemplate('Users/index.twig', ['users' => $users]);
    }
    public function delete()
    {
        $id = $_GET['id'];
        Models\User::deleteUser($id);
        $this->indexAction();
    }
    public function sort()
    {
        $sortFlag = $_POST['sort'];
        $users = Models\User::getAll($sortFlag);
        View::renderTemplate('Users/index.twig', ['users' => $users]);
    }
}
