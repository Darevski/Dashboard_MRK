<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 11:56
 */

namespace Application\Controllers\Dashboard;

use Application\Controllers\Controller_dashboard;
use Application\Exceptions\UFO_Except;

/**
 * Class Controller_professors
 * Действия с преподавателями (пользователь без привелегий)
 * @package Controllers\Dashboard
 */
class Controller_professors extends Controller_dashboard
{
    /**
     * Выводит Json строку содержающую информацию о местонахождении преподавателя на текущее время
     *
     * Входной параметр через JSON строку(POST) integer 'professor_id'
     *
     * Структура:
     * lesson_num,name,department
     * state = now/next/false
     *  now - на текущий момент времени идет пара
     *  next - возвращена следующая пара
     *  false - пар на сегодня нету
     * group_number, lesson_name, classroom,start_time,end_time
     * [state] = 'success' || [state] = 'fail' && [message] = string ....
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_professor_state(){
        //$_POST['json_input'] = '{"professor_id":1}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['professor_id'])) {
                $professor_id = (integer)$data['professor_id'];
                $result_professor = $this->professor_model->get_professor_state($professor_id);
                $this->view->output_json($result_professor);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }

    /**
     * Выводит Json строку содержающую информацию о рассписании преподавателя на неделю
     *
     * Входной параметр через JSON строку(POST) integer 'professor_id'
     *
     * Со следующей структурой:
     * even/uneven { day { lesson_num { group_number,lesson_name } }
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_professor_timetable(){
        //$_POST['json_input'] = '{"professor_id":1}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);

            if (isset($data['professor_id'])) {
                $professor_id = (integer)$data['professor_id'];
                $professor_timetable = $this->professor_model->get_professor_timetable($professor_id);
                $this->view->output_json($professor_timetable);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }

    /**
     * Вывод Json строки, содержающей списки преподавателей
     *
     * Cо следующей структурой: уникальный id,professor(ФИО),depart_name(кафедра)
     * [state] = 'success'
     * @api
     */
    function action_get_list(){
        $list_professors =$this->professor_model->get_professors_list();
        $this->view->output_json($list_professors);
    }
}