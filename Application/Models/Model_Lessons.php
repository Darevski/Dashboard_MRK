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
        $lessons_query = "SELECT lessons_list.id ,lessons_list.name,lessons_list.department_code FROM lessons_list";
        $lessons = $this->database->getAll($lessons_query);
        return $lessons;
    }

}