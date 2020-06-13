<?php
// error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);

if (!defined('NGR_DOCUMENT_ROOT')) {
    define('NGR_DOCUMENT_ROOT', dirname(__FILE__) == '/' ? '' : dirname(__FILE__));
}

include_once('src/NagmapReborn/ConfigLoader.php');
?>
<!DOCTYPE html>

<html lang="<?php echo config('ngreborn.language', 'en-US') ?>">

<head>
    <link rel="manifest" href="manifest.json" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="resources/img/NagFavIcon.ico" />
    <link rel="stylesheet" href="resources/materialize/materialize.min.css?v=<?php echo config('ngreborn.version', rand()) ?>">
    <link rel="stylesheet" href="resources/materialize/materialicons.css?v=<?php echo config('ngreborn.version', rand()) ?>">
    <link rel="stylesheet" href="resources/css/style.css?v=<?php echo config('ngreborn.version', rand()) ?>" />
    <link rel="stylesheet" href="resources/css/animate.css?v=<?php echo config('ngreborn.version', rand()) ?>" />
    <link rel="stylesheet" href="resources/leaflet/leaflet.css?v=<?php echo config('ngreborn.version', rand()) ?>" />
    <title>Nagmap Reborn - v<?php echo config('ngreborn.version') ?></title>
</head>

<body>
    <div id="debug"></div>

    <div id="error_button" class="hidden">
        <a class="btn-floating btn-large waves-effect waves-light red modal-trigger" href="#modal_error" onclick="$('#modal_error').modal().modal('open');"><i class="material-icons">error_outline</i></a>
    </div>

    <div id="modal_error" class="modal modal-fixed-footer"></div>

    <div class="debug_console" id="debug_console">
        <div>
            <a onclick="$('#debug_console').toggleClass('open')" class="waves-effect waves-light red btn" style="width:100%" data-i18n="close"></a>
        </div>
        <div id="console_text"></div>
    </div>

    <div class="animated" id="cover">
        <div id="cover_error" class="hidden"></div>
        <div class="slide" id="welcome">
            <div class="inside animated fadeIn delay-05s slow">
                <svg width="600px" height="600px" clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" version="1.1" viewbox="0 0 295.9 295.9" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                    <?php echo @file_get_contents("resources/img/NagmapR-Logo.svg"); ?>
                </svg>
                <h2 id="cover_msg" class="animated fadeIn delay-2s fast"></h2>
                <h2 id="cover_msg_error" class="hidden"></h2>
            </div>
        </div>
    </div>

    <div id="map"></div>

    <div id="changesbar">
        <div class="form-group" id="filter"></div>
        <div id="downHosts"></div>
        <div id="critHosts"></div>
        <div id="warHosts"></div>
    </div>

    <script>
        // Setting initial parameters.
        var config = {},
            _paq = [],
            i18nConfig = {},
            generalStatus = 0,
            tp = null,
            tooLong = null,
            alertAudio = null,
            waitConfigInterval,
            _u,
            firstRadial = 1,
            secondRadial = 44,
            direction = "up",
            realTime = true,
            STATUS = {
                GENERAL: {
                    accessDenied: -4,
                    incompatible: -3,
                    tooLong: -2,
                    generealError: -1,
                    initial: 0,
                    finished: 1
                },
                HOSTS: {
                    up: 0,
                    warning: 1,
                    critical: 2,
                    down: 3
                }
            };
    </script>

    <script src="resources/js/i18next.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/js/jquery-i18next.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/js/typed.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/js/jquery.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/materialize/materialize.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/js/popper.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/js/tippy.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/leaflet/leaflet.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/leaflet/oms.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/leaflet/leaflet.smoothmarkerbouncing.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/sa/sweetalert2.all.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/ns/jquery.nicescroll.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/js/axios.min.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>

    <script src="resources/classes/Utils.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/classes/Host.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/classes/NagmapReborn.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
    <script src="resources/js/app.js?v=<?php echo config('ngreborn.version', rand()) ?>"></script>
</body>

</html>