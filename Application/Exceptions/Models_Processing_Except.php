<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 27.02.16
 * Time: 17:22
 */

namespace Application\Exceptions;
use Application\Core\View;

/**
 * Class Models_Processing_Except
 * Обработчик ошибок возникающих в процессе обработки данных
 * @package Exceptions
 */
class Models_Processing_Except extends Main_Except
{
    /**
     * Вывод ошибок в браузер
     */
    public function output_error(){
        $output['state'] = 'fail';
        $output['message'] = self::getMessage();
        View::output_json($output);
    }
}