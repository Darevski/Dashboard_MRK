<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 9:21
 */
namespace Application\Controllers;
use Application\Core;
use Application\Models;

class Controller_Dashboard extends Core\Controller
{
    function __construct(){
        $this->view = new Core\View();
        $this->model = new Models\Model_Dashboard();
    }
    function action_start()
    {
        $this->view->generate('Main_view.php', 'Tmp_view.php');
    }
}