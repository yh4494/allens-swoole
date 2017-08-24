<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/14
 * Time: 下午6:55
 */

namespace Core;


use Core\Contacts\Server;
use Core\Library\ASwooleValidator;
use Core\Library\SimpleInjection;

class SwooleServer extends Server
{
    protected $server   = null;  //Server instance
    protected $port     = null;  //Server port|如果为0表示随机端口
    protected $validate = null;
    protected $app      = null;  //容器

    private static $instance   = null;

    private function __construct(SimpleInjection $app)
    {
        $this->port     = env('MYSQL_POOL_PORT', 9527);
        $this->validate = new ASwooleValidator();
        $this->app      = $app;
    }

    static function getInstance($app){
        if (is_null(self::$instance)){
            self::$instance = new static($app);
        }
        return self::$instance;
    }

    /**
     * 启动服务
     */
    public function serverStart()
    {
        $this->server   = new \swoole_server(env('SERVER_IP', '127.0.0.1'), $this->port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->server->set(
            require APP_PATH . '/config/server.php'
        );
        $this->server->on('Receive', [$this, 'my_onReceive']);
        $this->server->on('Finish' , [$this, 'my_onFinish']);
        $this->server->on('Close'  , [$this, 'my_onClose']);

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
        $controllerPath = 'App\Controllers';
        echo $data . PHP_EOL;
        while (true){
            $data = $this->validate->validateCall($data);
            if ($data['flag']){         //验证参数
                $serv->send($fd, response_json(-6, $data['msg'], '参数错误'));
                break;
            }

            $class = $controllerPath . "\\" . $data['data']['controller'];

            if (!$this->validate->ServerValidator($serv, $fd, $class, $data['data']['method'])){
                break;
            }

            $result = $this->app->call($class, $data['data']['method'], $data['data']['args']);

            $serv->send($fd,  $result);
            break;
        }

    }

    function my_onFinish($serv, $data)
    {
        echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
    }

    function my_onClose($server, $fd, $rectorId){
        echo '-------------请求结束-------------' . PHP_EOL;
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