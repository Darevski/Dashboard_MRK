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
        $this->professor_model = new Models\Model_Professors();
        $this->timetable_model = new Models\Model_TimeTable();
    }

    function action_start()
    {
        $this->view->generate();
    }

    /**
     * Возвращает Json строку содержающую информацию о местонахождении преподавателя на текущее время
     * name,department,lesson_num,
     * state = now/next/false
     * now - на текущий момент времени идет пара
     * next - возвращена следующая пара
     * false - пар на сегодня нету
     * group_number, lesson_name, classroom
     * @param int $professor_id - уникальный номер преподавателя
     */
    function action_get_professor_state($professor_id=7){
        $result_professor = $this->professor_model->get_professor_state($professor_id);
        $this->view->output_json($result_professor);
    }

    /**
     * Выводит Json строку содержающую информацию о рассписании преподавателя на неделю
     * Со следующей структурой
     * even/uneven{
     *  day{
     *      lesson_num{
     *          group_number
     *          lesson_name
     *      }
     * }
     */
    function action_get_professor_timetable($professor_id=7){
        $professor_timetable=$this->professor_model->get_professor_timetable($professor_id);
        $this->view->output_json($professor_timetable);
    }

    /**
     * Получение списка преподавателей уникальный id,professor(ФИО),depart_name(кафедра)
     * вывод в виде json строки
     */
    function action_get_list_professors(){
        $list_professors =$this->professor_model->get_professors_list();
        $this->view->output_json($list_professors);
    }

    /**
     * Получение списка всех групп (курса группы) и вывод в виде json строки
     */
    function action_get_list_group(){
        $list_group=$this->timetable_model->get_list_group();
        $this->view->output_json($list_group);
    }

    /**
     * Получение рассписания на сегодня/следующий учебный день для указанной группы
     * и вывод в виде json строки
     */
    function action_actual_dashboard(){
        $_POST['group_number']='32494';
        if (isset($_POST['group_number'])){
            $group_number = $this->security_variable($_POST['group_number']);
            $dashboard = $this->timetable_model->get_actual_dashboard($group_number);
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
            $dashboard = $this->timetable_model->get_week_timetable($group_number);
            $this->view->output_json($dashboard);
        }
    }

}