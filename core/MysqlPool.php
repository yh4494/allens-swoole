<?php

namespace Core;

use Core\Contacts\pool;

class MysqlPool extends pool {

	protected $server   = null;  //Server instance
    protected $port     = null;  //Server port|如果为0表示随机端口

	private static $instance   = null;

	private function __construct()
    {
        $this->port     = env('MYSQL_POOL_PORT', 9527);
    }

    static function getInstance(){
        if (is_null(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function serverStart()
    {
        $this->server   = new \swoole_server(env('SERVER_IP', '127.0.0.1'), $this->port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->server->set(
            require '../config/pool.php'
        );
        $this->server->on('Receive', [$this, 'my_onReceive']);
        $this->server->on('Task'   , [$this, 'my_onTask']);
        $this->server->on('Finish' , [$this, 'my_onFinish']);

        echo 'swoole 服务已经启动......' . PHP_EOL;
        $this->server->start();
    }

    /**
     * 监听数据接收事件
     *
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data    接收到的数据
     *
     */
    function my_onReceive($serv, $fd, $from_id, $data)
    {
        //taskwait就是投递一条任务，这里直接传递SQL语句了,然后阻塞等待SQL完成
        $result = $serv->taskwait($data);
        if ($result !== false) {
            list($status, $db_res) = explode(':', $result, 2);
            if ($status == 'OK') {
                //数据库操作成功了，执行业务逻辑代码，这里就自动释放掉MySQL连接的占用
                $serv->send($fd, var_export(unserialize($db_res), true) . "\n");
            } else {
                $serv->send($fd, $db_res);
            }
            return;
        } else {
            $serv->send($fd, "Error. Task timeout" . PHP_EOL);
        }
    }

    function my_onTask($serv, $task_id, $from_id, $sql)
    {
        static $link = null;
        if ($link == null) {
            $link = mysqli_connect("172.16.1.25", "tc_net", "net_test", "net", '3306');
            if (!$link) {
                $link = null;
                $serv->finish("ER:" . mysqli_error($link));
                return;
            }
        }
        fwrite(STDOUT, $sql . PHP_EOL );
        $result = $link->query($sql);
        if (!$result) {
            $serv->finish("ER:" . mysqli_error($link));
            return;
        }
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $serv->finish("OK:" . serialize($data));
    }

    function my_onFinish($serv, $data)
    {
        echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
    }

    function reload(){
        $this->server->reload();
        opcache_reset();
    }

    function __destruct()
    {
        $this->server->close();
    }
}

