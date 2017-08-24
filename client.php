<?php

define('APP_PATH', __DIR__);

require 'bootstrap/app.php';

$client = new \ASwooleClient\ClientServer();

$client->connect('127.0.0.1', 9027);

//$client->exec('select * from steward limit 1', function($result){
//    echo $result;
//});

//$client->call('hello', 'IndexController', [], function($data, $code){
//    echo $data;
//});

$client->hello();

//$client->call('hello', 'IndexController', [], function($data, $error){
//    var_dump($data);
//});