<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/17
 * Time: 下午5:08
 */

namespace Core\Contacts;


abstract class Server
{
    private $server = null;

    abstract function serverStart();
}