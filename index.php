<?php
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
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
    <link rel="stylesheet" href="resources/style.css?v=<?php echo file_get_contents("VERSION");?>" />
    <link rel="stylesheet" href="resources/animate.css" />
    <link rel="stylesheet" href="resources/toastr/toastr.css" />
    <link rel="stylesheet" href="resources/sa/sweetalert2.min.css" />
    <link rel="stylesheet" href="resources/leaflet/leaflet.css" />
    <title>NagMap Reborn</title>
</head>

<body>
    <div id="debug"> </div>

    <div class="animated" id="cover">
        <div id="cover_error" class="hide"></div>
        <!-- Load page -->
        <div class="slide" id="welcome">
            <div class="inside animated fadeIn delay-05s slow">
                <svg width="600px" height="600px" clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" version="1.1" viewbox="0 0 295.9 295.9" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                    <?php echo file_get_contents("resources/img/NagmapR-Logo.svg"); ?>
                </svg>
                <h2 id="cover_msg" class="animated fadeIn delay-2s fast"></h2>
                <h2 id="cover_msg_error" class="hide"></h2>
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

    <script src="resources/typed.js"></script>

    <script src="debugInfo/resources/js/jquery.min.js"></script>

    <script src="debugInfo/resources/js/popper.min.js"></script>
    <script src="resources/tippy.min.js"></script>

    <script src="resources/leaflet/leaflet.js"></script>
    <script src="resources/leaflet/oms.js"></script>
    <script src="resources/leaflet/leaflet.smoothmarkerbouncing.js"></script>

    <script src="resources/toastr/toastr.min.js"></script>
    <script src="resources/sa/sweetalert2.all.min.js"></script>

    <script src="resources/class/Utils.js"></script>
    <script src="resources/class/Host.js"></script>
    <script src="resources/class/NagmapReborn.js"></script>
    <script src="resources/class/main.js"></script>

    <script>
        try {
            var tempHostsInfo = <?php echo json_encode($data); ?>;

            var config = {
                debug: <?php echo ($nagMapR_Debug); ?>,
                mapCenter: [<?php echo ($nagMapR_MapCentre); ?>],
                mapDefaultZoom: <?php echo ($nagMapR_MapZoom); ?>,
                mapTiles: "<?php echo ($nagMapR_LeafletStyle); ?>",
                locale: "<?php echo ($nagMapR_Lang); ?>",
                cbMode: <?php echo ($nagMapR_ChangesBarMode); ?>,
                cbSize: <?php echo ($nagMapR_ChangesBarSize); ?>,
                cbFilter: <?php echo ($nagMapR_BarFilter); ?>,
                cbFontSize: <?php echo ($nagMapR_FontSize); ?>,
                dtFormat: <?php echo ($nagMapR_DateFormat); ?>,
                soundAlert: <?php echo ($nagMapR_PlaySound); ?>,
                iconStyle: <?php echo ($nagMapR_IconStyle); ?>,
                showLines: <?php echo ($nagMapR_Lines); ?>,
                updateTime: <?php echo ($nagMapR_TimeUpdate); ?>,
                secKey: "<?php echo ($nagMapR_key); ?>"
            };

            var i18nConfig = {
                lng: "<?php echo ($nagMapR_Lang); ?>",
                debug: true,
                resources: {
                    "<?php echo ($nagMapR_Lang); ?>": {
                        translation: <?php echo file_get_contents("langs/$nagMapR_Lang.json"); ?>
                    }
                }
            };
        } catch (e) {
            Utils.initErrorHandler();
        }
    </script>
</body>

</html> 