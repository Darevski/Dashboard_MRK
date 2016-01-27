<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 09.01.16
 * Time: 1:38
 */

namespace Application\Models;

/**
 * Класс логики связанный со списком групп, его изменение и вывод.
 * Class Model_ListGroups
 * @package Application\Models
 *
 */
class Model_List_Groups extends Model_Dashboard
{
    /**
     * Вносит в список групп групу с указанным курсом
     * @param integer $grade - курс
     * @param integer $group_number - номер групы
     *
     * @return mixed array string state:success||fail , string message
     */
    public function group_add($grade,$group_number){
        // Проверка на уже записанную группу
        $search_query = "SELECT * FROM groups_list WHERE group_number = ?i";
        $result_of_search = $this->database->getAll($search_query,$group_number);

        if (count($result_of_search)==0){
            $query = "INSERT INTO groups_list SET grade=?i,group_number=?i";
            $this->database->query($query,$grade,$group_number);
            $result["state"] = "success";
        }

        else{
            $result["state"] = "fail";
            $result["message"] = "Группа №$group_number уже существует в списке";
        }
        return $result;
    }

    /**
     * Возвращает список групп по возрастанию с разбиением на курсы
     * @return array номер группы + курс
     */
    public function get_list_group(){
        $result = [];
        $result_query=$this->database->getALL("SELECT group_number,grade FROM groups_list");
        //сортировка полученного списка в соответсвии с их номером по возрастанию
        usort($result_query,array($this,'Groups_Sort_CallBack'));
        foreach ($result_query as $value){
            $grade = $value['grade'];
            $result[$grade][] = $value['group_number'];
        }
        return $result;
    }

    /**
     * Возврашает список групп по возрастанию
     * @return array номера групп
     */
    public function get_list_group_without_grade(){
        $result = [];
        $result_query=$this->database->getALL("SELECT group_number,grade FROM groups_list");
        //сортировка полученного списка в соответсвии с их номером по возрастанию
        usort($result_query,array($this,'Groups_Sort_CallBack'));

        foreach ($result_query as $value)
            $result['groups'][]=$value['group_number'];
        return $result;
    }

    /**
     * Callback функция для сортировки списка групп по их номеру
     * @param array $a ячейка группы
     * @param array $b ячейка группы
     * @return int результат сравнения номера группы
     */
    private function Groups_Sort_CallBack($a, $b) {
        if ($a['group_number'] == $b['group_number']) {
            return 0;
        }
        return ($a['group_number'] < $b['group_number']) ? -1 : 1;
    }
}