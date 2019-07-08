<?php
//error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
include_once('validateAndVerify.php');
include_once('marker.php');
?>
<!DOCTYPE html>

<html>

<head>
    <link rel="manifest" href="manifest.json" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="resources/img/NagFavIcon.ico" />
    <link rel="stylesheet" href="resources/materialize/materialize.min.css">
    <link rel="stylesheet" href="resources/materialize/materialicons.css">
    <link rel="stylesheet" href="resources/style.css?v=<?php echo file_get_contents("VERSION"); ?>" /> <!-- To avoid cache problems -->
    <link rel="stylesheet" href="resources/animate.css" />
    <link rel="stylesheet" href="resources/toastr/toastr.css" />
    <link rel="stylesheet" href="resources/sa/sweetalert2.min.css" />
    <link rel="stylesheet" href="resources/leaflet/leaflet.css" />
    <title>Nagmap Reborn</title>
</head>

<body>
    <div id="debug"> </div>

    <div id="error_button" class="hidden">
        <a class="btn-floating btn-large waves-effect waves-light red modal-trigger" href="#modal_error" onclick="$('#modal_error').modal().modal('open');"><i class="material-icons">error_outline</i></a>
    </div>

    <div id="modal_error" class="modal modal-fixed-footer">
    </div>

    <div class="debug_console" id="debug_console">
        <div>
            <a onclick="$('#debug_console').toggleClass('open')" class="waves-effect waves-light red btn" style="width:100%" data-i18n="close"></a>
        </div>
        <div id="console_text">
        </div>
    </div>

    <div class="animated" id="cover">
        <div id="cover_error" class="hidden"></div>
        <!-- Load page -->
        <div class="slide" id="welcome">
            <div class="inside animated fadeIn delay-05s slow">
                <svg width="600px" height="600px" clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" version="1.1" viewbox="0 0 295.9 295.9" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                    <?php echo file_get_contents("resources/img/NagmapR-Logo.svg"); ?>
                </svg>
                <h2 id="cover_msg" class="animated fadeIn delay-2s fast"></h2>
                <h2 id="cover_msg_error" class="hidden"></h2>
            </div>
        </div>
        <!-- End load page -->
    </div>
    <div id="map"> </div>

    <!-- <div class="form-group" id="filter"> </div> -->

    <div id="changesbar" style="font-size:<?php echo $nagMapR_FontSize; ?>px;">
        <div class="form-group" id="filter"></div>
        <div id="downHosts"></div>
        <div id="critHosts"></div>
        <div id="warHosts"></div>
    </div>

    <script src="resources/i18next.min.js"></script>
    <script src="resources/jquery-i18next.min.js"></script>

    <script src="resources/typed.js"></script>

    <script src="debugInfo/resources/js/jquery.min.js"></script>

    <script src="resources/materialize/materialize.min.js"></script>

    <script src="debugInfo/resources/js/popper.min.js"></script>
    <script src="resources/tippy.min.js"></script>

    <script src="resources/leaflet/leaflet.js"></script>
    <script src="resources/leaflet/oms.js"></script>
    <script src="resources/leaflet/leaflet.smoothmarkerbouncing.js"></script>

    <script src="resources/toastr/toastr.min.js"></script>
    <script src="resources/sa/sweetalert2.all.min.js"></script>

    <script src="resources/Class/Utils.js"></script>
    <script src="resources/Class/Host.js"></script>
    <script src="resources/Class/NagmapReborn.js"></script>
    <script src="resources/Class/main.js"></script>

    <script>
        try {
            var tempHostsInfo = <?php echo json_encode($data); ?>;

            var config = {
                debug: parseInt('<?php echo ($nagMapR_Debug); ?>' || 0),
                mapCenter: [<?php echo ($nagMapR_MapCentre); ?>] || [-6.469293, -50.913464],
                mapDefaultZoom: parseFloat('<?php echo ($nagMapR_MapZoom); ?>' || 6.1),
                mapTiles: "<?php echo ($nagMapR_LeafletStyle); ?>" || "//{s}.tile.osm.org/{z}/{x}/{y}.png",
                locale: "<?php echo ($nagMapR_Lang); ?>" || "en-US",
                cbMode: parseInt('<?php echo ($nagMapR_ChangesBarMode); ?>' || 0),
                cbSize: parseInt('<?php echo ($nagMapR_ChangesBarSize); ?>' || 25),
                cbFilter: parseInt('<?php echo ($nagMapR_BarFilter); ?>' || 0),
                cbFontSize: parseInt('<?php echo ($nagMapR_FontSize); ?>' || 25),
                dtFormat: parseInt('<?php echo ($nagMapR_DateFormat); ?>' || 1),
                soundAlert: parseInt('<?php echo ($nagMapR_PlaySound); ?>' || 0),
                iconStyle: parseInt('<?php echo ($nagMapR_IconStyle); ?>' || 0),
                showLines: parseInt('<?php echo ($nagMapR_Lines); ?>' || 0),
                updateTime: parseInt('<?php echo ($nagMapR_TimeUpdate); ?>' || 15),
                secKey: "<?php echo ($nagMapR_key); ?>" || "s9Yqz7Ox9pgpYx5cVinh7Iez4ZY29KGqqx9SlxSDbxmRHWgkjuLjogOIz4WFGuFQy2EOwKBJo6AA5UQY1IArMgsiR7KQwXyB"
            };

            var i18nConfig = {
                lng: "<?php echo ($nagMapR_Lang); ?>",
                debug: config.debug,
                resources: {
                    "<?php echo ($nagMapR_Lang); ?>": {
                        translation: <?php echo file_get_contents("langs/$nagMapR_Lang.json"); ?>
                    }
                }
            };
        } catch (e) {
            Utils.initErrorHandler(e);
        }
    </script>
</body>

</html>