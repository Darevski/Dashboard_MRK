<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 12:16
 */

namespace Application\Controllers\Dashboard;
use Application\Controllers\Controller_dashboard;

/**
 * Class Controller_units
 * Отвечает за подразделения, спциальности и т.д.
 * @package Application\Controllers\Dashboard
 */
class Controller_units extends Controller_dashboard
{
    /**
     * Возвращает список отделений с их кодом
     * {name:" ", code " "}
     * [state] = 'success'
     * @api
     */
    function action_get_faculty_list(){
        $list=$this->depart_list->get_faculty_list();
        $this->view->output_json($list);
    }

    /**
     * Возвращает список специальностей с их кодом
     * {name:" ", code " "}
     * [state] = 'success'
     * @api
     */
    function action_get_spec_list(){
        $list=$this->depart_list->get_specializations_list();
        $this->view->output_json($list);
    }

    /**
     * Возвращает список кафедр с их кодом
     * {name:" ", code " "}
     * [state] = 'success'
     * @api
     */
    function action_get_depart_list(){
        $list=$this->depart_list->get_departments_list();
        $this->view->output_json($list);
    }
}