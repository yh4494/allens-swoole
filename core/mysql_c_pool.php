<?php

namespace Core;

class mysql_c_pool extends pool {

	protected $task_num = null;
	protected $server   = null;
	protected $work_num = null;

	private static $instance   = null;

	private function __construct()
    {
        $this->task_num = env('MYSQL_POOL_TASK_NUM',50);
        $this->work_num = env('MYSQL_POOL_WORK_NUM', 8);
    }

    static function getInstance(){
        if (is_null(self::$instance)){
            self::$instance = new mysql_c_pool();
        }
        return self::$instance;
    }

    public function serverStart()
    {
        $this->server   = new \swoole_server(env('SERVER_IP', '127.0.0.1'), 9580, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

        $this->server->set(array(
            'worker_num'      => $this->work_num,
            'task_worker_num' => $this->task_num, //MySQL连接的数量
        ));

        $this->server->on('Receive', [$this, 'my_onReceive']);
        $this->server->on('Task'   , [$this, 'my_onTask']);
        $this->server->on('Finish' , [$this, 'my_onFinish']);

        $this->server->start();

        echo 'swoole 服务已经启动';
    }

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
            $serv->send($fd, "Error. Task timeout\n");
        }
    }

    function my_onTask($serv, $task_id, $from_id, $sql)
    {
        static $link = null;
        if ($link == null) {
            $link = mysqli_connect("127.0.0.1", "root", "", "test", '3306');
            if (!$link) {
                $link = null;
                $serv->finish("ER:" . mysqli_error($link));
                return;
            }
        }
        fwrite(STDOUT, '\r\n' . $sql);
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
}

