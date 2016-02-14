<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 9:21
 * @author Darevski
 */
namespace Application\Controllers;
use Application\Core;
use Application\Models;

/**
 * Контроллер базовых функций рассписания
 * Class Controller_Dashboard
 * @package Application\Controllers
 */
class Controller_Dashboard extends Core\Controller
{
    /**
     * Подключение моделей рассписания + преподавателей
     */
    function __construct(){
        $this->view = new Core\View();
        $this->professor_model = new Models\Model_Professors();
        $this->timetable_model = new Models\Model_TimeTable();
        $this->list_group_model = new Models\Model_List_Groups();
        $this->notification_model = new Models\Model_Notifications();
        $this->depart_list = new Models\Model_List_Departments();
    }

    /**
     * Стартовое действие (по-умолчанию)
     */
    function action_start()
    {
        $this->view->generate("Dashboard_View.php");
    }

    /**
     * Выводит Json строку содежащую уведомления для указанной группы
     *
     * Входной параметр через JSON строку(POST) integer 'group_number'
     *
     * Структура результата:
     * {string 'state' critical|warning|info , string text}
     *
     * @api
     */
    function action_get_notifications_by_group(){
        //$_POST['json_input'] = '{"group_number":"32494"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['group_number'])) {
                $group_number = $this->security_variable($data['group_number']);
                $notification = $this->notification_model->get_notification_for_group($group_number);
                $this->view->output_json($notification);
            }
        }
    }

    /**
     * Выводит Json строку содержающую информацию о местонахождении преподавателя на текущее время
     *
     * Входной параметр через JSON строку(POST) integer 'professor_id'
     *
     * Структура:
     * lesson_num,name,department
     * state = now/next/false
     * now - на текущий момент времени идет пара
     * next - возвращена следующая пара
     * false - пар на сегодня нету
     * group_number, lesson_name, classroom,start_time,end_time
     *
     * @api
     */
    function action_get_professor_state(){
        //$_POST['json_input'] = '{"professor_id":"1"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['professor_id'])) {
                $professor_id = $this->security_variable($data['professor_id']);
                $result_professor = $this->professor_model->get_professor_state($professor_id);
                $this->view->output_json($result_professor);
            }
        }
    }

    /**
     * Выводит Json строку содержающую информацию о рассписании преподавателя на неделю
     *
     * Входной параметр через JSON строку(POST) integer 'professor_id'
     *
     * Со следующей структурой:
     * even/uneven { day { lesson_num { group_number,lesson_name } }
     *
     * @api
     */
    function action_get_professor_timetable(){
        //$_POST['json_input'] = '{"professor_id":"1"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['professor_id'])) {
                $professor_id = $this->security_variable($data['professor_id']);
                $professor_timetable = $this->professor_model->get_professor_timetable($professor_id);
                $this->view->output_json($professor_timetable);
            }
        }
    }

    /**
     * Вывод Json строки, содержающей списко преподавателей
     *
     * Cо следующей структурой: уникальный id,professor(ФИО),depart_name(кафедра)
     *
     * @api
     */
    function action_get_list_professors(){
        $list_professors =$this->professor_model->get_professors_list();
        $this->view->output_json($list_professors);
    }

    /**
     * Вывод Json строки, содежащей список групп + их курс
     * integer grade { integer group_number }
     *
     * @api
     */
    function action_get_list_group(){
        $list_group=$this->list_group_model->get_list_group();
        $this->view->output_json($list_group);
    }

    /**
     * Вывод Json строки, содержащий рассписание на учебный/следующий день для указанной группы
     *
     * Входной параметр через JSON строку(POST) integer 'group_number'
     *
     * Структура результата: {string today/tomorrow {
     * string day_name,
     * integer lesson_number{
     * - string lesson_name,
     * - string professor,
     * - bool true|false state : пара идет(следующая на очереди)/пары кончились } } }
     *
     * @api
     */
    function action_get_actual_dashboard(){
        //$_POST['json_input'] = '{"group_number":"32494"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['group_number'])) {
                $group_number = $this->security_variable($data['group_number']);
                $dashboard = $this->timetable_model->get_actual_dashboard($group_number);
                $this->view->output_json($dashboard);
            }
        }
    }

    /**
     * Вывод рассписания Json строкой для выбранной группы на неделю (числитель + знаменатель) для указанной группы
     *
     * Входной параметр через JSON строку(POST) integer 'group_number'
     *
     * Структура результата: {string 'even'/'uneven' {
     *
     * integer day {
     *
     * integer lesson_number | null {
     * - string lesson_name
     * - string professor_name
     * } } }
     * @api
     */
    function action_get_week_dashboard(){
        //$_POST['json_input'] = '{"group_number":"32494"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['group_number'])) {
                $group_number = $this->security_variable($data['group_number']);
                $dashboard = $this->timetable_model->get_week_timetable($group_number);
                $this->view->output_json($dashboard);
            }
        }
    }

    /**
     * По ввведеным данным(номер группы, номер пары), выводит информацию о паре
     *
     *
     * Входной параметр через JSON строку(POST) integer 'group_number' & integer 'lesson_number'
     *
     * Структура результата: {
     * - classroom,
     * - lesson_name,
     * - department - кафдера преподавателя,
     * - professor - фио преподавателя,
     * - professor_id - id преподавателя,
     * - photo_url - url фото преподавателя,
     * - time - время в которое идет пара}
     * @api
     */
    function action_get_lesson_info(){
        //$_POST['json_input'] = '{"group_number":"32494","lesson_number":"4"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['group_number']) & isset($data['lesson_number'])) {

                $number_group = $this->security_variable($data['group_number']);
                $lesson_number = $this->security_variable($data['lesson_number']);

                $result = $this->timetable_model->get_lesson_info_by($number_group, $lesson_number);
                $this->view->output_json($result);
            }
        }
    }

    /**
     * Вывод Json строки, содержащей даты учебных занятий для определенной группы
     * integer group_number
     * Результат:{
     * days:["Y-m-d",.....]
     * }
     *
     */
    function action_get_working_days_group_for_month(){
        //$_POST['json_input'] = '{"group_number":"32494"}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            if (isset($data['group_number'])) {
                $number_group = $this->security_variable($data['group_number']);
                $result = $this->timetable_model->get_working_days_group_for_month($number_group);
                $this->view->output_json($result);
            }
        }
    }

    /**
     * Вывод Json строки, содержащей отсортированный по возрастанию список групп
     * integer group_number
     *
     * @api
     */
    function action_get_list_group_without_grade(){
        $list_group=$this->list_group_model->get_list_group_without_grade();
        $this->view->output_json($list_group);
    }

    /**
     * Возвращает список групп под указанные фильтры
     * grade - курс группы ["1".."4"]
     * class -  после какого класса группа 9/11
     * spec - специальность группы
     * faculty - отделение группы
     * @api
     */
    function action_get_filtered_groups()
    {
        //$_POST['json_input'] = '{"grade":"null","class":["9"],"spec":["4","5","7"],"faculty":["1","2"]}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            if (isset ($data['grade']) || isset($data['class'])||isset($data['specialization'])||isset($data['faculty'])){
                $data=$this->secure_array($data);
                $result = $this->list_group_model->get_groups_by_filter($data);
                $this->view->output_json($result);
            }
        }
    }

    /**
     * Возвращает список отделений с их кодом
     * {name:" ", code " "}
     * @api
     */
    function action_get_faculty_list(){
        $list=$this->depart_list->get_faculty_list();
        $this->view->output_json($list);
    }

    /**
     * Возвращает список специальностей с их кодом
     * {name:" ", code " "}
     * @api
     */
    function action_get_specializations_list(){
        $list=$this->depart_list->get_specializations_list();
        $this->view->output_json($list);
    }

    /**
     * Возвращает список кафедр с их кодом
     * {name:" ", code " "}
     * @api
     */
    function action_get_departments_list(){
        $list=$this->depart_list->get_departments_list();
        $this->view->output_json($list);
    }
}