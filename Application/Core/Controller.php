<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 * @author Darevski
 */
namespace Application\Core;

use Application\Models;
use Application\Exceptions\UFO_Except;

/**
 * Базовый класс контролера используется для расширения наследованием
 * Class Controller
 * @package Application\Core
 */
class Controller {
    // Переменные с объектами моделей
    /**
     * @var Models\Model_List_Departments
     */
    protected $depart_list;
    /**
     * @var Models\Model_Notifications
     */
    protected $notification_model;
    /**
     * @var Models\Model_List_Groups
     */
    protected $list_group_model;
    /**
     * @var Models\Model_TimeTable
     */
    protected $timetable_model;
    /**
     * @var Models\Model_Professors
     */
    protected $professor_model;
    /**
     * @var View
     */
    protected $view;

    /**
     * Модель авторизационных данных
     * @var Models\Base\Model_Auth
     */
    protected $auth_model;

    /**
     * Создает объекты моделей
     * @see $professor_model - объект логики, связанный с преподавателями
     * @see $timetable_model - объект логики, связанный с рассписанием
     * @see $list_group_model - объект логики, связанный с группами
     * @see $notification_model - объект логики, связанный с уведомлениями
     * @see $depart_list - объект логики, связанный с кафедрами/отделениями
     * @see $auth_model - объект модели авторизации
     * @see $model - объект базовой модели
     */
    function __construct(){
        $this->professor_model = new Models\Model_Professors();
        $this->timetable_model = new Models\Model_TimeTable();
        $this->list_group_model = new Models\Model_List_Groups();
        $this->notification_model = new Models\Model_Notifications();
        $this->depart_list = new Models\Model_List_Departments();
        $this->auth_model= new Models\Base\Model_Auth();
        $this->view = new View();
    }

    /**
     * Удаление из строки спецсимволов, тэгов и т.д.
     * @param string $variable входное значение строки
     * @return string $result результат преобразования
     */
    protected function security_variable($variable){
        $result=htmlentities($variable);
        $result=strip_tags($result);
        return $result;
    }

    /**
     * Функция для рекурсивной обработки массивов
     * Удаляет спецсимволы тэги и т.д. из значений
     * @param $array
     * @return mixed
     */
    protected function secure_array($array){
        foreach ($array as &$value){
            if (is_array($value))
                $value=$this->secure_array($value);
            else{
                $value=htmlentities($value);
                $value=strip_tags($value);
            }
        }
        unset($value);
        return $array;
    }

    /**
     * Проверка на сущуствование сессии для пользователя
     * Проверка хэша пользователя, при не совпадении хэша очистка сессии в бд и на стороне клинета
     * @return bool|string сессия не существует / значение привелегии
     * @throws UFO_Except При не совпадении хэша, вброс исключения, очистка сессии, очистка хэша в БД
     */
    public function state_authorization(){
        if (isset($_SESSION['login']) & isset($_SESSION['hash'])) {
            $result = $this->auth_model->take_privilege($_SESSION['login'], $_SESSION['hash']);
            if ($result == false) { // при не совпадении очистка сесии в БД
                $this->auth_model->clear_hash($_SESSION['login']);
                throw new UFO_Except ('Доступ заблокирован, несовпадение контрольного хэша, перезайдите в систему', 601);
            }
            return $result;
        }
        else
            return false;
    }
}