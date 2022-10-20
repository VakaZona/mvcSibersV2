<?php

namespace App\Controllers;

use App\Models;
use Core\View;

class Users extends Authenticated
{
    public function indexAction()
    {
        $kol = 5;
        if (isset($_GET['sort'])){
            $sortFlag = $_GET['sort'];
        } else {
            $sortFlag = [];
        }
        $count = Models\User::countRowInDb();
        $countPage = ceil($count/$kol);
        if (isset($_GET['page'])){
            $page = $_GET['page'];
        } else {
            $page = 1;
        }

        $users = Models\User::getAll($sortFlag, $page, $kol);
        View::renderTemplate('Users/index.twig', ['users' => $users, 'countRow' => $count, 'countPage'=> $countPage, 'sortFlag'=>$sortFlag] );
    }
    public function delete()
    {
        $id = $_GET['id'];
        Models\User::deleteUser($id);
        $this->indexAction();
    }
    //No use
    public function sort()
    {
        $sortFlag = $_POST['sort'];
        $users = Models\User::getAll($sortFlag);
        View::renderTemplate('Users/index.twig', ['users' => $users, 'sortFlag'=>$sortFlag]);
    }

}
