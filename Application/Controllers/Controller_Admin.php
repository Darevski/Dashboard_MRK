<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 18:43
 */

namespace Application\Controllers;
use Application\Core;
use Application\Exceptions\UFO_Except;
use Application\Models;

class Controller_Admin extends Core\Controller{
    function __construct(){
        parent::__construct();
        $this->validate();
        $this->model = new Models\Model_Admin();
    }

    /**
     * При наличии у пользователя модификатора доступа пользователя пропускает дальше
     * В противном случае выбрасывается исключение
     * @throws UFO_Except
     */
    private function validate(){
        // Получение значения привелегии
        $result = $this->state_authorization();
        if ($result !== 'Admin')
            throw new UFO_Except ('У вас не достаточно прав для просмотра данного контента', 403);
    }
    public function action_start(){
       //$this->model->group_add(32494,'ch',1,2,'apanasevich','oaip');
    }
}