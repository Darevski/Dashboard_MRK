<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 */
namespace Application\Core;
use Application\Models;
class Controller {

    public $model;
    public $view;
    public $auth_model;

    function __construct()
    {
        $this->view = new View();
        $this->auth_model = new Models\Model_Auth();
    }
}