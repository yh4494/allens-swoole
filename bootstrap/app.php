<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/14
 * Time: 下午7:01
 */

require 'functions.php';

require APP_PATH . '/vendor/autoload.php';

$app = new \Core\Library\SimpleInjection();

$app->bind(\Core\Library\Toolers::class);