<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 09.01.16
 * Time: 1:38
 */

namespace Application\Models;

use Application\Models\Base\Model_Dashboard;
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
        $result_query=$this->database->getALL("SELECT group_number FROM groups_list");
        //сортировка полученного списка в соответсвии с их номером по возрастанию
        usort($result_query,array($this,'Groups_Sort_CallBack'));

        foreach ($result_query as $value)
            $result['groups'][]=$value['group_number'];
        return $result;
    }

    /**
     * Возвращает список групп отсортированный по возрастанию на основании переданных фильтров
     * @param $filter - массив фильтров курс{1,..,4}, класс поступления после 9/11, специальность зашифрованнная в коде
     * отделение защифрованное в коде
     * @return array список групп подходящий под заданные параметры / список всех групп && bool select_all = true
     */
    public function get_groups_by_filter($filter){
        $string_filter = [];
        $result=[];
        $surname = array ('grade_filter','class_filter','specialization_filter','faculty_filter');

        /**
         * Создание частей SQL строки для сравнения полученных данных с данными содержащимися в БД
         * общий вид ( параметр = значение OR параметр = значение ....)
         */
        // Фильтр групп по курсу
        if (isset($filter['grade']) && is_array($filter['grade']) && count($filter['grade'])>0){
            $grade_filter = $this->make_filter($filter['grade'],'grade');
            $string_filter[] = 0;
        }

        // Фильтр по поступлению после 9/11 класса
        if (isset($filter['class']) && is_array($filter['class']) && count($filter['class'])>0){
            $class_filter = $this->make_filter($filter['class'],'class');
            $string_filter[] = 1;
        }

        // Фильтр по специальностям
        if (isset($filter['spec']) && is_array($filter['spec']) && count($filter['spec'])>0) {
            $specialization_filter = $this->make_filter($filter['spec'],'specialization');
            $string_filter[] = 2;
        }

        // Фильтр по отделениям
        if (isset($filter['faculty']) && is_array($filter['faculty']) && count($filter['faculty'])>0){
            $faculty_filter = $this->make_filter($filter['faculty'],'faculty');
            $string_filter[] = 3;
        }

        $count = count($string_filter);
        // при наличии хотя бы 1 фильтра выполняется дальнейшее построение запроса
        if ($count > 0) {
            /**
             * Формирование строки для непосредственного запроса в БД
             * Используя полученные выше кусочки строк sql запроса между которыми вставляется AND
             */
            $i = 0;
            $query = "SELECT * FROM groups_list WHERE ";
            foreach ($string_filter as $value) {
                ++$i;
                $query .= $$surname[$value];
                if ($i != $count)
                    $query .= " AND ";
            }
            $group_list = $this->database->getAll($query);
            //сортировка полученного списка групп по возрастанию
            usort($group_list, array($this, 'Groups_Sort_CallBack'));
            foreach ($group_list as $value)
                $result['groups'][] = $value['group_number'];

            // Если выбранны все группы добавление параметра
            if ($result === $this->get_list_group_without_grade())
                $result['selected_all'] = true;
        }
        // при отсутсвии фильтров возвращаются все группы
        else{
            $result = $this->get_list_group_without_grade();
            $result['selected_all'] = true;
        }
        return $result;
    }

    /**
     * Функция создающая строку фильтра для обращения к БД
     * @internal Application\Models\Model_List_Groups::get_groups_by_filter()
     *
     * @param $filter - массив с данными фильтра
     * @param $name - выражение(имя поля) в бд с которым происходит сравнение
     * @return string готовая строка вида ($name = value OR $name = value ..... )
     */
    private function make_filter($filter,$name){
        $filter_query = '(';
        $count = count($filter);
        $i=0;
        foreach($filter as $value){
            ++$i;
            $filter_query.=$this->database->parse("?p = ?i",$name,$value);
            if ($i !=$count)
                $filter_query.=" OR ";
            else
                break;
        }
        $filter_query.=')';
        return $filter_query;
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