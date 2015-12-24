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
        $this->view->output_json($list_group);
    }

    /** Получение списка преподавателей уникальный id + фио + кафедра вывод в виде json строки */
    function action_get_list_professors(){
        $list_professors =$this->model->get_professors_list();
        $this->view->output_json($list_professors);
    }

    /**
     * Получение рассписания на сегодня/следующий учебный день для указанной группы
     * и вывод в виде json строки
     */
    function action_actual_dashboard(){
        $_POST['group_number']='32494';
        if (isset($_POST['group_number'])){
            $group_number = $this->security_variable($_POST['group_number']);
            $dashboard = $this->model->get_actual_dashboard($group_number);
            $this->view->output_json($dashboard);
        }

    }

    /**
     * Получение рассписания на неделю (числитель + знаменатель) для указанной группы
     * и вывод в виде json строки
     */
    function action_week_dashboard(){
        $_POST['group_number']='32494';
        if (isset($_POST['group_number'])){
            $group_number = $this->security_variable($_POST['group_number']);
            $dashboard = $this->model->get_week_timetable($group_number);
            $this->view->output_json($dashboard);
        }
    }

}