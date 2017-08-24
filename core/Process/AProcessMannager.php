<?php
/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/21
 * Time: 下午6:08
 */

namespace Core\Process;


class AProcessMannager
{

    protected $workers  = [];
    protected $pids     = [];
    private static $ins = null;
    protected $worknum  = 0;

    public function initWorkers(callable $task, callable $response)
    {
        foreach ($this->workers as $item){
            $pr  = new AProcess($task, true);
            $pid = $pr->start();
            $this->workers[$pid] = $pr;
        }

        foreach ($this->workers as $process){
            swoole_event_add($process->pipe, function ($pipe) use($process, $response){
                $data = $process->read();
                if (isset($response)){
                    $response($data);
                }
            });
        }
    }

    /**
     * @return null|static
     */
    public static function share($worknum){
        if (is_null(self::$ins)){
            self::$ins = new static();
            self::$ins->worknum = $worknum;
        }
        return self::$ins;
    }


}