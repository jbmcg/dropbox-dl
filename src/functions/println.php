<?php

function println($str = '', ...$args)
{
    $params = [$str];
    foreach ($args as $arg) {
        $params[] = $arg;
    }
    call_user_func_array('printf', $params);
    print(PHP_EOL);
}

