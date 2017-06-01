<?php

$client = new \Core\client_class();

$client->connect();

$client->client->send('select * from person');

?>
