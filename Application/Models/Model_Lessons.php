<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 06.03.16
 * Time: 0:52
 */

namespace Application\Models;


use Application\Models\Base\Model_Dashboard;
use Application\Exceptions\Models_Processing_Except;

/**
 * Class Model_Lessons
 * Отвечает за логику предметов
 * @package Application\Models
 */
class Model_Lessons extends Model_Dashboard{

    /**
     * Возвращает массив предметов id, название, отношение к кафедре
     * @return array
     */
    public function get_list_lessons(){
        $lessons_query = "SELECT lessons_list.id as lesson_id,lessons_list.lesson_name,lessons_list.department_code FROM lessons_list";
        $lessons = $this->database->getAll($lessons_query);
        return $lessons;
    }

    /**
     * Проверяет существование предмета с указанным ID
     * @param $lesson_id int
     * @return bool true - существует, false - нет
     * @throws Models_Processing_Except
     */
    public function is_lesson_set($lesson_id){
        if (!is_int($lesson_id))
            throw new Models_Processing_Except("Идентификатор предмета $lesson_id не является числом");
        $search_query = "Select * From lessons_list WHERE id = ?i";
        $result_query =$this->database->getAll($search_query,$lesson_id);
        if (count($result_query)>0)
            return true;
        else
            return false;
    }

}