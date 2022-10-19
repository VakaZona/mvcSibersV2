<?php

namespace App\Controllers;

use App\Auth;
use Core\View;

class Item extends Authenticated
{

    public function indexAction()
    {
        View::renderTemplate('Item/index.twig');
    }
    public function newAction()
    {
        echo "new action";
    }
    public function showAction()
    {
        echo "show action";
    }

}
