<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:35
*/

namespace Application;

include_once 'Core/Psr4AutoLoaderClass.php';

$loader = new Core\Psr4AutoloaderClass();
$loader ->register();
$loader->addNamespace('Application\Core',$_SERVER['DOCUMENT_ROOT'].'/Application/Core');
$loader->addNamespace('Application\Controllers',$_SERVER['DOCUMENT_ROOT'].'/Application/Controllers');
$loader->addNamespace('Application\Exceptions',$_SERVER['DOCUMENT_ROOT'].'/Application/Exceptions');
$loader->addNamespace('Application\Models',$_SERVER['DOCUMENT_ROOT'].'/Application/Models');


$router = new Core\AltoRouter();

$router->map('GET|POST','/', 'home');
$router->map('GET|POST','/[a:controller]/[a:action]','service');
$router->addRoutes(array(
    array('GET|POST','/admin/[a:controller]/[a:action]', 'admin'),
    array('GET|POST','/admin/','admin#start'),
    array('GET|POST','/admin','admin#start')
));

$router->addRoutes(array(
    array('GET|POST','/dashboard/[a:controller]/[a:action]','dashboard'),
    array('GET|POST','/dashboard/','dashboard#start'),
    array('GET|POST','/dashboard','dashboard#start')
));
// Просмотр текущего запроса

$route_result = $router->match();

Try{
    $Route = new Core\Route();
    $Route->start($route_result);
}
catch (Exceptions\UFO_Except $error){
    $error->classification_error($error);
}
catch (Exceptions\SQL_Except $error){
    $error->output_error();
    $error->log_errors();
}
catch (Exceptions\Models_Processing_Except $error){
    $error->output_error();
}
catch (\Exception $error){
    echo $error->getMessage();
}