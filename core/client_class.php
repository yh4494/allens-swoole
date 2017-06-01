<?php

namespace Core;

class client_class{

    public $client;

    public function __construct() {
        $this->client = new \swoole_client(SWOOLE_SOCK_TCP);
    }
    public function connect() {
        $this->client->on('connect', [$this, 'deal_connect']);

//        $this->client->on("close", function($cli){
//            $cli->close(); // 1.6.10+ 不需要
//            echo "close\n";
//        });
//
//        $this->client->on("error", function($cli){
//            exit("error\n");
//        });

        if( !$this->client->connect("127.0.0.1", 9501 , 1) ) {
            echo "Error: {$this->client->errMsg}[{$this->client->errCode}]\n";
        }
    }

    function deal_connect($cli){
        echo '客户端连接已经创建';
    }
}