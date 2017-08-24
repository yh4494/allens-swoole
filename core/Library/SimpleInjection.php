<?php

/**
 * Created by PhpStorm.
 * User: yueyu
 * Date: 2017/8/18
 * Time: 上午9:48
 */

namespace Core\Library;

class SimpleInjection
{
    protected $ins       = [];
    protected $alias     = [];
    protected $bindings  = [];
    protected $abstracts = [];

    public function bind($abstract, $class = null, $alias = null)
    {
        $this->make($abstract, $class, $alias);
    }

    /**
     * @param string $abstract
     * @param null $ins
     * @param null $alias
     */
    protected function make(string $abstract, $ins = null, $alias = null)
    {
        $ins_class = null;
        $ins_s     = null;

        if (is_object($ins)){
            $ins_class = get_class($ins);
            $ins_s     = $ins;
        }

        if (is_string($ins)){
            $ins_class = $ins;
            $ins_s     = $this->instance($ins);
        }

        if (is_null($ins)){
            $ins_class = $abstract;
            $ins_s     = $this->instance($abstract);
        }

        if ($ins_s){
            $this->ins[$ins_class]      = $ins_s;
            $this->abstracts[$abstract] = $ins_class;
            $this->bindings[$abstract]  = $ins_s;
        }
    }

    /**
     * 获取实例
     *
     * @param $abstract
     * @param null $class
     * @return mixed|null
     */
    public function getInstance($abstract, $class = null)
    {
        if (!$class){
            if (isset($this->bindings[$abstract]) && $i = $this->bindings[$abstract]){
                return $i;
            }
        }

        if (isset($this->ins[$class]) && $i = $this->ins[$class]){
            return $i;
        }

        $this->make($abstract, $class);
        return $this->getInstance($abstract, $class);
    }

    /**
     * 创建实例
     *
     * @param $className
     * @return null
     */
    protected function instance($className)
    {
        if (class_exists($className)){
            return new $className();
        }

        return null;
    }

    /**
     * 注意到达这里的时候就说明controller和method已经验证完成
     *
     * @param $controller
     * @param $method
     */
    public function call($controller, $method, $args = [])
    {
        $ins = $this->getInstance($controller);
        if (method_exists($ins, $method)){
            return call_user_func([$ins, $method], $args);
        }

        return null;
    }
}