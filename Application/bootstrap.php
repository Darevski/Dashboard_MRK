<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:35

require_once 'Core/Model.php';
require_once 'Core/View.php';
require_once 'Core/Controller.php';

require_once 'Controllers/Controller_UFO.php';

require_once 'Exceptions/Main_Except.php';
require_once 'Exceptions/UFO_Except.php';

require_once 'Core/Route.php';
 */

namespace Application;

include_once 'Core/Psr4AutoLoaderClass.php';

$loader = new Core\Psr4AutoloaderClass();
$loader ->register();
$loader->addNamespace('Application\Core',$_SERVER['DOCUMENT_ROOT'].'Application/Core');
$loader->addNamespace('Application\Controllers',$_SERVER['DOCUMENT_ROOT'].'Application/Controllers');
$loader->addNamespace('Application\Exceptions',$_SERVER['DOCUMENT_ROOT'].'Application/Exceptions');
$loader->addNamespace('Application\Models',$_SERVER['DOCUMENT_ROOT'].'Application/Models');

$Route = new Core\Route();
$Route->start();
