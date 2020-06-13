<?php
if (!defined("CONFIG_LOAD")) {

    function loadConfig($envs, $joinKey = '')
    {
        foreach ($envs as $key => $value) {
            if (is_array($value)) {
                loadConfig($value, "{$joinKey}{$key}.");
            } else {
                putenv("NGR_{$joinKey}{$key}={$value}");
            }
        }
    }

    function config($key, $default = null)
    {
        $value = getenv("NGR_{$key}");

        if ($value === false)
            $value = $default;

        return (is_numeric($value) ? (is_float($value) ? ((float) $value) : ((int) $value)) : $value);
    }

    if (file_exists(NGR_DOCUMENT_ROOT . '/config.php')) {
        $envs = include(NGR_DOCUMENT_ROOT . '/config.php');

        if (is_array($envs))
            loadConfig($envs);
    }

    putenv("NGR_NGREBORN.VERSION=" . @file_get_contents(NGR_DOCUMENT_ROOT . "/VERSION"));
    putenv("NGR_NGREBORN.DOMAIN=" . @file_get_contents(NGR_DOCUMENT_ROOT . "/resources/reporter/DOMAIN"));

    define("CONFIG_LOAD", true);
}
