<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 11:40
 */

namespace Application\Controllers\Dashboard;


use Application\Controllers\Controller_dashboard;
use Application\Exceptions\UFO_Except;

/**
 * Class Controller_groups
 * Действия с группами (пользователь без привелегий)
 * @package Application\Controllers\Dashboard
 */
class Controller_groups extends Controller_dashboard
{
    /**
     * Вывод Json строки, содежащей список групп + их курс
     * integer grade { integer group_number }
     * [state] = 'success'
     * @api
     */
    function action_get_list(){
        $list_group=$this->list_group_model->get_list_group();
        $this->view->output_json($list_group);
    }

    /**
     * Вывод Json строки, содержащей отсортированный по возрастанию список групп
     * integer group_number
     * [state] = 'success'
     * @api
     */
    function action_get_list_without_grade(){
        $list_group=$this->list_group_model->get_list_group_without_grade();
        $this->view->output_json($list_group);
    }

    /**
     * Возвращает список групп под указанные фильтры && selected_all = true при выводе всех групп
     * integer grade - курс группы ["1".."4"]
     * integer class -  после какого класса группа 9/1
     * integer spec - специальность группы
     * integer faculty - отделение группы
     * [state] = 'success'
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_filter_apply()
    {
        //$_POST['json_input'] = '{"grade":"null","class":[9],"spec":[4,5,7],"faculty":[1,2]}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            if (isset ($data['grade']) || isset($data['class']) || isset($data['specialization']) || isset($data['faculty'])) {
                $data = $this->secure_array($data);
                $result = $this->list_group_model->get_groups_by_filter($data);
                $this->view->output_json($result);
            } else
                throw new UFO_Except("Неверные параметры запроса", 400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }
}