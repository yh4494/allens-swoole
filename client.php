<?php

define('APP_PATH', __DIR__);

require('./Lib/functions.php');

$client = new \Core\client_class();

$client->client->on("connect", function($cli) {
    $cli->send("hello world\n");
});

$client->client->on("receive", function($cli, $data = ""){
    $data = $cli->recv(); //1.6.10+ 不需要
    if(empty($data)){
        $cli->close();
        echo "closed\n";
    } else {
        echo "received: $data\n";
        sleep(1);
        $cli->send("hello\n");
    }
});

$client->client->on("close", function($cli){
    $cli->close(); // 1.6.10+ 不需要
    echo "close\n";
});

$client->client->on("error", function($cli){
    exit("error\n");
});


$client->connect();

$client->client->send('select * from person');

?>
