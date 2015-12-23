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
        $this->view->generate();
    }

    /**
     * Получение списка всех групп (курса группы) и вывод в виде json строки
     */
    function action_get_list_group(){
        $list_group=$this->model->get_list_group();
        $list_group=json_encode($list_group);
        $this->view->output_json($list_group);
    }
}