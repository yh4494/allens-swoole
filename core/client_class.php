<?php

namespace Core;

class client_class{

    public $client;

    public function __construct() {
        $this->client = new \swoole_client(SWOOLE_SOCK_TCP);
    }
    public function connect() {
        if( !$this->client->connect(env('SERVER_IP', '127.0.0.1'), 9580 , 1) ) {
            echo "Error: {$this->client->errMsg}[{$this->client->errCode}]\n";
        }
    }

    function deal_connect($cli){
        echo '客户端连接已经创建';
    }
}