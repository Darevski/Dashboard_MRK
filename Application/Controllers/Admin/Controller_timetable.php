<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.03.16
 * Time: 21:44
 */

namespace Application\Controllers\Admin;

use Application\Controllers\Controller_admin;
use Application\Exceptions\UFO_Except;

/**
 * Class Controller_timetable
 * Отвечает за расписание, его редоктирование удаление и т.д.
 * @package Application\Controllers\Admin
 */
class Controller_timetable extends Controller_admin{
    /**
     * Заносит в БД рассписание на определенный день
     * структура Json строки:
     * group_number integer - номер групппы
     * numerator string - нумератор недели "ch/zn/all"
     * day_number integer - номер дня недели
     * timetable array рассписани на день структура: [{prof_id,lesson_id,num_lesson}]
     * @throws UFO_Except
     * @throws \Application\Exceptions\Models_Processing_Except
     * @api
     */
    public function action_set_timetable_for_day(){
        $_POST['json_input'] = '{"group_number":"32494","numerator":"all","day_number":"2","timetable":[{"prof_id":3,"lesson_id":4,"num_lesson":5}]}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            // Удаление запрещенных символов
            $data = $this->secure_array($data);
            if (isset($data['group_number']) && isset($data['numerator']) && isset($data['day_number']) &&
                isset($data['timetable'])){
                $group_number = (integer)$data['group_number'];
                $day_number = (integer)$data['day_number'];
                $numerator = $data['numerator'];
                $timetable = $data['timetable'];

                $result = $this->timetable_model->set_timetable_for_day($group_number,$numerator,$day_number,$timetable);

                $this->view->output_json($result);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }

}