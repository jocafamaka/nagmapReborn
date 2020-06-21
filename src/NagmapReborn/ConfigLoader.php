<?php
if (!defined("CONFIG_LOAD")) {

    function loadConfig($envs, $joinKey = '')
    {
        $canOverwrite = [
            'general.debug',
            'map.centre',
            'map.zoom',
            'ngreborn.filter_hostgroup',
            'ngreborn.filter_service',
            'ngreborn.changes_bar.mode',
            'ngreborn.changes_bar.size',
            'ngreborn.changes_bar.font_size',
            'ngreborn.changes_bar.filter',
            'ngreborn.priorities.unknown',
            'ngreborn.priorities.up',
            'ngreborn.priorities.warning',
            'ngreborn.priorities.critical',
            'ngreborn.priorities.down',
            'ngreborn.play_sound',
            'ngreborn.default_icon_style',
            'ngreborn.lines'
        ];

        foreach ($envs as $key => $value) {
            if (is_array($value)) {
                loadConfig($value, "{$joinKey}{$key}.");
            } else {
                if (defined("ALLOW_OVERWRITE") && ALLOW_OVERWRITE && in_array($joinKey . $key, $canOverwrite) && isset($_GET[str_replace(".", "_", $joinKey . $key)])) {
                    $value = $_GET[str_replace(".", "_", $joinKey . $key)];
                }
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

        if (is_array($envs)) {
            define("ALLOW_OVERWRITE", (@$envs['security']['allow_overwrite'] == 1));
            loadConfig($envs);
        }
    }

    putenv("NGR_NGREBORN.VERSION=" . @file_get_contents(NGR_DOCUMENT_ROOT . "/VERSION"));
    putenv("NGR_NGREBORN.DOMAIN=" . @file_get_contents(NGR_DOCUMENT_ROOT . "/resources/reporter/DOMAIN"));

    define("CONFIG_LOAD", true);
}
