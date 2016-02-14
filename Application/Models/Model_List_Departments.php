<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.02.16
 * Time: 17:02
 */

namespace Application\Models;

use Application\Models\Base\Model_Dashboard;

/**
 * Class Model_List_Departments
 * @package Application\Models
 */
class Model_List_Departments extends Model_Dashboard
{
    /**
     * Возвращает список отделений
     * @return array
     */
    public function get_faculty_list(){
        $query = "SELECT faculty_list.name,code FROM faculty_list";
        $result=$this->database->getAll($query);
        return $result;
    }

    /**
     * Возвращает список кафедр
     * @return array
     */
    public function get_departments_list(){
        $query = "SELECT id,depart_name FROM departments_list";
        $result=$this->database->getAll($query);
        return $result;
    }

    /**
     * Вовзращает список специальностей
     * @return array
     */
    public function get_specializations_list(){
        $query = "SELECT specialization_list.name,code FROM specialization_list";
        $result=$this->database->getAll($query);
        return $result;
    }

}