<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 12:05
 */

namespace Application\Controllers\Dashboard;
use Application\Controllers\Controller_dashboard;
use Application\Exceptions\UFO_Except;

/**
 * Class Controller_timetable
 * Отвечает за действия с рассписанием (без привелегий)
 * @package Application\Controllers\Dashboard
 */
class Controller_timetable extends Controller_dashboard
{
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
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_actual(){
        //$_POST['json_input'] = '{"group_number":32494}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['group_number'])) {
                $group_number = (integer)$data['group_number'];
                $dashboard = $this->timetable_model->get_actual_dashboard($group_number);
                $this->view->output_json($dashboard);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
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
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_week(){
        //$_POST['json_input'] = '{"group_number":32494}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['group_number'])) {
                $group_number = (integer)$data['group_number'];
                $dashboard = $this->timetable_model->get_week_timetable($group_number);
                $this->view->output_json($dashboard);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
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
     * - professor_name - фио преподавателя,
     * - professor_id - id преподавателя,
     * - photo_url - url фото преподавателя,
     * - time - время в которое идет пара}
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_lesson_info(){
        //$_POST['json_input'] = '{"group_number":32494,"lesson_number":6}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['group_number']) & isset($data['lesson_number'])) {

                $number_group =(integer) $data['group_number'];
                $lesson_number =(integer) $data['lesson_number'];

                $result = $this->timetable_model->get_lesson_info_by($number_group, $lesson_number);
                $this->view->output_json($result);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }

    /**
     * Вывод Json строки, содержащей даты учебных занятий для определенной группы
     * integer group_number
     * Результат:{
     * days:["Y-m-d",.....]
     * }
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_working_days_for_month(){
        //$_POST['json_input'] = '{"group_number":32494}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['group_number'])) {
                $number_group = (integer)$data['group_number'];
                $result = $this->timetable_model->get_working_days_group_for_month($number_group);
                $this->view->output_json($result);
            } else
                throw new UFO_Except("Неверные параметры запроса", 400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }
}