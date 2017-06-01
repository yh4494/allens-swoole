<?php

spl_autoload_register(function($class){
    $namespaceArr = explode('\\', $class);
    $name         = $namespaceArr[count($namespaceArr) - 1];

    require APP_PATH . '/core/' . $name . '.php';
});

function getConfigs(){
    $env_path = APP_PATH . '/.env';
    $o        = @fopen($env_path, 'r');
    $env      = [];

    if ($o) {
        while (($buffer = fgets($o, 4096)) !== false) {
            $arr = explode('=', trim($buffer));
            if (!empty($arr[0]) && !empty($arr))
                $env[$arr[0]] = isset($arr[1]) ? trim($arr[1]) : '';
        }
        if (!feof($o)) {
            echo "Error: unexpected fgets() fail\n";
        }
    }
    fclose($o);
    return $env;
}

function env($key, $default = ''){
    $config = getConfigs();
    if (isset($config[$key]) && !empty($config[$key])){
        return $config[$key];
    }
    return $default;
}