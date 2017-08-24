<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/14
 * Time: 下午6:57
 */

return [
//    'reactor_num'     => 2,
//    'backlog'         => 128,                     //Listen队列长度,此参数将决定最多同时又多少个待accept的连接
//    'max_request'     => 50,                      //worker进程在处理完n次请求后结束运行。manager会重新创建一个worker进程。防止内存溢出
//    'max_conn'        => 100000,                  //最大连接数
//    'daemonize'       => 1,                       //守护进程化,加入此参数后,执行 php server.php 将转入后台作为守护进程运行
//    'reactor'         => 2,                       //线程数
    'log_file'        => APP_PATH . '/logs/swoole.log',
//    'heartbeat_check_interval' => 30,             //每隔多少秒检测一次,单位秒,Swoole会轮询所有TCP连接
//    'heartbeat_idle_time'      => 60,             //TCP连接的最大闲置时间，单位s , 如果某fd最后一次发包距离现在的时间超过
//    'dispatch_mode'   => 1,                       //1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
//    'worker_num'      => $this->work_num,         //worker进程数,worker_num配置为CPU核数的1-4倍即可
//    'task_worker_num' => $this->task_num,         //MySQL连接的数量
];