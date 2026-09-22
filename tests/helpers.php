<?php

use Comento\SensAlimtalk\Test\TestConfig;

if (! function_exists('config')) {
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return TestConfig::all();
        }

        return TestConfig::get($key, $default);
    }
}
