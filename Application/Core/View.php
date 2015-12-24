<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 */

namespace Application\Core;

class View
{
    //public $template_view; // здесь можно указать общий вид по умолчанию.

    function generate($content_view='Blocks_view.php', $auth_view='AuthForm_view.php', $template_view='Tmp_view.php', $data = null){
        include 'Application/Views/'.$template_view;
    }

    function display($content_view,$data = null){
        include 'Application/Views/'.$content_view;
    }
    
    function output_json($value){
        $json=json_encode($value,JSON_UNESCAPED_UNICODE );
        echo $json;
    }
}