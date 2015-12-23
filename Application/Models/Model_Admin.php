<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 23:26
 */

namespace Application\Models;


use Application\Core\Model;

class Model_Admin extends Model
{
    /**
     * Добавляет рассписание для указанной группы, на указанный день, номер пары
     * @param $group_number
     * @param $numerator    - числитель/знаменатель недели
     * @param $day_number
     * @param $lesson_number
     * @param $professor_name
     * @param $lesson_name
     */
    function group_add($group_number,$numerator,$day_number,$lesson_number,$professor_name,$lesson_name)
    {
        $query = "INSERT INTO groups SET group_number=?s,numerator=?s,day_number=?s,lesson_number=?s,professor=?s,lesson_name=?s";
        $this->database->query($query,$group_number,$numerator,$day_number,$lesson_number,$professor_name,$lesson_name);
    }


}