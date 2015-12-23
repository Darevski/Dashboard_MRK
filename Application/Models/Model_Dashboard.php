<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 22.09.15
 * Time: 14:29
 */

namespace Application\Models;
use Application\Core;

class Model_Dashboard extends Core\Model
{
    /**
     * Получает список всех групп (курс группы).
     * @return array с номер группы и курсом
     */
    function get_list_group(){
        $result=$this->database->getALL("SELECT group_number,grade FROM groups_list");
        return $result;
    }

}