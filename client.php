<?php

define('APP_PATH', __DIR__);

require('./Lib/functions.php');

$client = new \Core\client_class();

$client->connect();

$client->client->send('select * from person');

echo $client->client->recv();

?>
