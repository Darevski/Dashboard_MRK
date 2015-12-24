<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 23:26
 * @author Darevski
 */

namespace Application\Models;


use Application\Core\Model;

/**
 * Набор логики обеспечивающей действия администратора
 * Class Model_Admin
 * @package Application\Models
 */
class Model_Admin extends Model
{
    /**
     * Добавляет в БД запись о паре на указанный день
     * @param integer $group_number номер пары
     * @param string $numerator 'ch'|'zn'|'all'
     * @param integer $day_number номер дня
     * @param integer $lesson_number номер пары
     * @param string $professor_name имя преподавателя
     * @param $lesson_name название пары
     * @api
     */
    function group_add($group_number,$numerator,$day_number,$lesson_number,$professor_name,$lesson_name)
    {
        $query = "INSERT INTO groups SET group_number=?s,numerator=?s,day_number=?s,lesson_number=?s,professor=?s,lesson_name=?s";
        $this->database->query($query,$group_number,$numerator,$day_number,$lesson_number,$professor_name,$lesson_name);
    }


}