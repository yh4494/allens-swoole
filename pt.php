<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/21
 * Time: 上午11:45
 */

define('APP_PATH', __DIR__);

require 'bootstrap/app.php';

$process = new \Core\Process\AProcess(function (swoole_process $process) {
    $process->write('Hello');
}, true);

$process->start();
usleep(100);

echo $process->read(); // 输出 Hello