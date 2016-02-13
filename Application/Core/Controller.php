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
    /**
     * переменная хранящая модель, под определенный класс
     * @var $model
     */
    protected $model;
    /**
     * переменная хранящая объект вида
     * @var $view
     */
    protected $view;

    /**
     * переменная хранящая авторизационную модель данных
     * @var
     */
    protected $auth_model;

    /**
     * Записывает объекты моделей в переменные
     * @see $auth_model - объект модели авторизации
     * @see $view - объект вида
     * @see $model - объект базовой модели
     */
    function __construct()
    {
        $this->model = new Model();
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

    /**
     * Выводит json строку со временем на сервере
     * string now_time
     */
    public function action_get_time(){
        $date['now_time'] = date("U");
        $this->view->output_json($date);
    }

}