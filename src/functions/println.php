<?php

function println()
{
    $args = func_get_args();
    $str = isset($args[0]) ? $args[0] : '';
    $params = array($str);
    foreach ($args as $argIndex => $arg) {
        if ($argIndex > 0) {
            $params[] = $arg;
        }
    }
    call_user_func_array('printf', $params);
    print(PHP_EOL);
}
