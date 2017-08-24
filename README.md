# Swoole功能扩展

* database pool
* rpc 使用json数据传输
* 异步进程

# 使用说明

server.php

~~~php
define('APP_PATH', __DIR__);
require 'bootstrap/app.php';
$server = \Core\SwooleServer::getInstance($app);
$server->serverStart();
~~~

client.php

~~~php
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

$client->hello(); //默认调用会进入到IndexController

//$client->call('hello', 'IndexController', [], function($data, $error){
//    var_dump($data);
//});
~~~

