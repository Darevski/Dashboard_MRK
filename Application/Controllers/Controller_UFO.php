<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 16.09.15
 * Time: 11:47
 */

namespace Application\Controllers;
use Application\Core;

class Controller_UFO extends Core\Controller
{
    function display($data){
        $this->view->display('UFO_view.php',$data);
    }
}