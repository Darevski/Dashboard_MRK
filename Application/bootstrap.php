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

Try{
    $Route = new Core\Route();
    $Route->start();
}
catch (Exceptions\UFO_Except $error){
    $error->classification_error($error);
}
catch (Exceptions\SQL_Except $error){
    $error->output_error();
    $error->log_errors();
}
catch (\Exception $error){
    echo $error->getMessage();
}
