<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 18:43
 */

namespace Application\Controllers;
use Application\Core;
use Application\Models;

class Controller_Admin extends Core\Controller{
    function __construct(){
        $this->view = new Core\View();
        $this->model = new Models\Model_Dashboard();
    }
}