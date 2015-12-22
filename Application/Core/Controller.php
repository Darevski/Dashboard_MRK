<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 */
namespace Application\Core;

class Controller {

    public $model;
    public $view;

    function __construct()
    {
        $this->view = new View();
    }
}