<?php

define('APP_PATH', __DIR__);

require 'bootstrap/app.php';

$server = \Core\SwooleServer::getInstance($app);

$server->serverStart();