<?php

/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/17
 * Time: 下午5:58
 */

namespace Core\Library;

class ASwooleValidator
{
    private $service = null;
    /**
     * ASwooleValidator constructor.
     */
    public function __construct()
    {
        $this->service = require 'Const.php';
    }

    public function validateCall($data){
        $data = (array)json_decode($data);
        $msg  = [];
        $flag = false;

        foreach ($this->service['call'] as $item){
            if (!isset($data[$item])){
                $msg[] = $item . '不存在';
                $flag  = true;
            }
        }

        return compact('msg', 'flag', 'data');
    }

    public function ServerValidator($serv, $fd, $class, $method){
        if (!class_exists($class)){ //验证Controller是否存在
            $serv->send($fd, response_json(-6, [], '控制器不存在'));
            return false;
        }

        if (!method_exists($class, $method)){
            $serv->send($fd, response_json(-6, [], '方法不存在'));
            return false;
        }

        return true;
    }
}