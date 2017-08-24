<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/17
 * Time: 下午5:12
 */

namespace App\Controllers;


class IndexController
{
    public function hello()
    {
        return response_json(0, ['just'], '哈哈你成功L');
    }

    public function timer(){

    }
}