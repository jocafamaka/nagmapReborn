<?php
/*
 * ##################################################################
 * #             ALL CREDITS FOR MODIFICATIONS ARE HERE             #
 * ##################################################################
 *
 * KEEP THE PATTERN
 *
 * Original Credits: Marcel Hecko (https://github.com/hecko) in 16 Oct 2014
 * Some changes: João Ribeiro (https://github.com/jocafamaka) in 06 March 2018
 *
 */

error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
$page = $_SERVER['PHP_SELF'];
$nagMapR_version = '1.5.0';
$nagMapR_CurrVersion = file_get_contents('https://pastebin.com/raw/HGUTiEtE'); //Get current version;
if($nagMapR_CurrVersion == "")  //Set local version in case of fail.
$nagMapR_CurrVersion = $nagMapR_version;
include('config.php');

// Check if the translation file informed exist.
if(file_exists("langs/$nagMapR_Lang.php"))
  include("langs/$nagMapR_Lang.php");
else
  die("$nagMapR_Lang.php does not exist in the languages folder! Please set the proper \$nagMapR_Lang variable in NagMap Reborn config file!");

// Validation of configuration variables.
if(!is_string($nagios_cfg_file)) 
  die("\$nagios_cfg_file $var_cfg_error ($nagios_cfg_file)");

if(!is_string($nagios_status_dat_file)) 
  die("\$nagios_status_dat_file $var_cfg_error ($nagios_status_dat_file)");

if(!is_string($nagMapR_Mapkey) || empty($nagMapR_Mapkey)) 
  die("\$nagMapR_Mapkey $var_cfg_error ($nagMapR_Mapkey)");

if(!is_string($nagMapR_FilterHostgroup)) 
  die("\$nagMapR_FilterHostgroup $var_cfg_error ($nagMapR_FilterHostgroup)");

if(!is_string($nagMapR_MapCentre)) 
  die("\$nagMapR_MapCentre $var_cfg_error ($nagMapR_MapCentre)");

if(!is_string($nagMapR_MapType)) 
  die("\$nagMapR_MapType $var_cfg_error ($nagMapR_MapType)");

if(!is_string($nagMapR_key)) 
  die("\$nagMapR_key $var_cfg_error ($nagMapR_key)");

if(!is_int($nagMapR_Debug))
  die("\$nagMapR_Debug $var_cfg_error ($nagMapR_Debug)");

if(!is_int($nagMapR_DateFormat))
  die("\$nagMapR_DateFormat $var_cfg_error ($nagMapR_DateFormat)");

if(!is_int($nagMapR_PlaySound))
  die("\$nagMapR_PlaySound $var_cfg_error ($nagMapR_PlaySound)");

if(!is_int($nagMapR_ChangesBar))
  die("\$nagMapR_ChangesBar $var_cfg_error ($nagMapR_ChangesBar)");

if(($nagMapR_ChangesBarMode < 1) || ($nagMapR_ChangesBarMode > 2))
  die("\$nagMapR_ChangesBarMode $var_cfg_error ($nagMapR_ChangesBarMode)");

if(($nagMapR_Reporting < 0) || ($nagMapR_Reporting > 1))
  die("\$nagMapR_Reporting $var_cfg_error ($nagMapR_Reporting)");

if(!is_int($nagMapR_Lines))
  die("\$nagMapR_Lines $var_cfg_error ($nagMapR_Lines)");

if(!( is_float($nagMapR_ChangesBarSize) || is_int($nagMapR_ChangesBarSize) ))
  die("\$nagMapR_ChangesBarSize $var_cfg_error ($nagMapR_ChangesBarSize)");

if(!( is_float($nagMapR_FontSize) || is_int($nagMapR_FontSize) ))
  die("\$nagMapR_FontSize $var_cfg_error ($nagMapR_FontSize)");

if(!( is_float($nagMapR_MapZoom) || is_int($nagMapR_MapZoom) ))
  die("\$nagMapR_MapZoom $var_cfg_error ($nagMapR_MapZoom)");

if(!( is_float($nagMapR_TimeUpdate) || is_int($nagMapR_TimeUpdate) ))
  die("\$nagMapR_TimeUpdate $var_cfg_error ($nagMapR_TimeUpdate)");

if($nagMapR_IconStyle > 2 || $nagMapR_IconStyle < 0)
  die("\$nagMapR_IconStyle $var_cfg_error ($nagMapR_IconStyle)");

if($nagMapR_Style != ''){
  if(!file_exists("styles/$nagMapR_Style.json")){
    die("\$nagMapR_Style $var_cfg_error ($nagMapR_Style)");
  }
}

include('marker.php');

if ($javascript == "") {
  echo $no_data_error;
  die("ERROR");
}

?>
<html>
<head>
  <link rel="shortcut icon" href="resources/img/NagFavIcon.ico" />
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link rel=StyleSheet href="resources/style.css" type="text/css" media=screen>
  <link href="resources/toastr/toastr.css" rel="stylesheet"/>
  <title>NagMap Reborn <?php echo $nagMapR_version ?></title>
  <script src="https://maps.google.com/maps/api/js?key=<?php echo $nagMapR_Mapkey; ?>" type="text/javascript"></script>

  <div id="myModal" class="modal">
    <div class="modal-content" id="modalContent">
      <div class="modal-header">
        <h2><?php echo $newVersion; ?> (<?php echo $nagMapR_CurrVersion; ?>)</h2>
      </div>
      <div class="modal-body">
        <?php echo $newVersionText; ?>
        <center><a href="https://www.github.com/jocafamaka/nagmapReborn/" target="_blank" style="cursor: pointer;"><img title="<?php echo $project; ?>" src="resources/img/logoBlack.svg" alt=""></a><center>
        </div>
        <div class="modal-footer">
          <button class="modal-btn" id="closeModal"><?php echo $close; ?></button>
        </div>
      </div>
    </div>

    <script type="text/javascript">

    // Defines the array used for the comparisons.
    var hostStatus = <?php echo json_encode($jsData); ?>;

    <?php
    if($nagMapR_ChangesBarMode == 2)
      echo ('

        var hostStatusPre = '. json_encode($jsData) .';

        var max;

        for (var ii = 1 ; ii < hostStatusPre.length ; ii++) {
          for (var i = 1 ; i < hostStatusPre.length ; i++) {
            if (hostStatusPre[i].time < hostStatusPre[i-1].time){
              max = hostStatusPre[i-1];
              hostStatusPre[i-1] = hostStatusPre[i];
              hostStatusPre[i] = max;
            }
          }
        }
        ');
    ?>

    //Defines the array that will contain the marks.
    var MARK = [];

    //Defines the array that will contain the Polylines.
    var LINES = [];

    //Define the source of the audio file.
    <?php
    if($nagMapR_PlaySound == 1)
      echo ("var audio = new Audio('Beep.mp3');\n")
    ?>

    //Define icons style.
    var iconRed = {
      url: 'resources/img/icons/MarkerRedSt-<?php echo $nagMapR_IconStyle; ?>.png',
      size: new google.maps.Size(29, 43),
      anchor: new google.maps.Point(14, 42)
    };

    var iconGreen = {
      url: 'resources/img/icons/MarkerGreenSt-<?php echo $nagMapR_IconStyle; ?>.png',
      size: new google.maps.Size(29, 43),
      anchor: new google.maps.Point(14, 42)
    };

    var iconOrange = {
      url: 'resources/img/icons/MarkerOrangeSt-<?php echo $nagMapR_IconStyle; ?>.png',
      size: new google.maps.Size(29, 43),
      anchor: new google.maps.Point(14, 42)
    };

    var iconYellow = {
      url: 'resources/img/icons/MarkerYellowSt-<?php echo $nagMapR_IconStyle; ?>.png',
      size: new google.maps.Size(29, 43),
      anchor: new google.maps.Point(14, 42)
    };

    var iconGrey = {
      url: 'resources/img/icons/MarkerGreySt-<?php echo $nagMapR_IconStyle; ?>.png',
      size: new google.maps.Size(29, 43),
      anchor: new google.maps.Point(14, 42)
    };

    //static code from index.php
    function initialize() {
      var myOptions = {
        zoom: <?php echo ("$nagMapR_MapZoom"); ?>,
        center: new google.maps.LatLng(<?php echo $nagMapR_MapCentre ?>),
        mapTypeId: google.maps.MapTypeId.<?php echo $nagMapR_MapType ?>,
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: false
      };

      window.map = new google.maps.Map(document.getElementById("map_canvas"),{
        zoom: <?php echo ("$nagMapR_MapZoom"); ?>,
        center: new google.maps.LatLng(<?php echo $nagMapR_MapCentre ?>),
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: false,
        <?php
        if($nagMapR_Style == ''){
          echo ('mapTypeId: google.maps.MapTypeId.'.$nagMapR_MapType);
        }
        else{
          echo ("styles: ");
          require "styles/$nagMapR_Style.json";
        }
        ?>
      });

// generating dynamic code from here
// if the page ends here, there is something seriously wrong, please contact joao_carlos.r@hotmail.com for help

//Filling array of Polylines
<?php
  // print the body of the page here
echo $linesArray;
echo ("\n\n");
echo $javascript;
echo ('};'); //end of initialize function
echo ('
  </script>
  </head>
  <body style="margin:0px; padding:0px; overflow:hidden;" onload="initialize()">');
if ($nagMapR_Debug == 1){
  echo ('<a href="debugInfo/index.php"><div href="debugInfo/index.php" id="div_fixa" class="div_fixa" style="z-index:2000;"><button class="button" style="vertical-align:middle"><span>Debug page</span></button></div></a>');
}
if ($nagMapR_ChangesBar == 1) {
  echo '<div id="map_canvas" style="width:100%; height:'.(100-$nagMapR_ChangesBarSize).'%; float: left"></div>';
  echo '<div id="changesbar" style="padding-top:2px; padding-left: 1px; background: black; height:'.$nagMapR_ChangesBarSize.'%; overflow:auto;">';
  if($nagMapR_ChangesBarMode == 2){
    echo('<div id="downHosts"></div><div id="critHosts"></div><div id="warHosts"></div>');
  }
  echo('</div>');
} else {
  echo '<div id="map_canvas" style="width:100%; height:100%; float: left"></div>';
}

?>

<script type="text/javascript">

    function now(){ //Return the formated date
      var date = new Date();   

      str_day = new String(date.getDate());
      str_month = new String((date.getMonth() + 1));
      year = date.getFullYear();

      str_hours = new String(date.getHours());
      str_minutes = new String(date.getMinutes());
      str_seconds = new String(date.getSeconds());

      if (str_day.length < 2) 
        str_day = 0 + str_day;

      if (str_month.length < 2) 
        str_month = 0 + str_month;

      if (str_hours.length < 2)
        str_hours = 0 + str_hours;

      if (str_minutes.length < 2)
        str_minutes = 0 + str_minutes;

      if (str_seconds.length < 2)
        str_seconds = 0 + str_seconds;

      <?php // Use the chosen format

      if($nagMapR_DateFormat == 1){
        echo ("return(date = str_day + '/' + str_month + '/' + year"); 
      } elseif ($nagMapR_DateFormat == 2) {
        echo ("return(date = str_month + '/' + str_day + '/' + year");
      }elseif ($nagMapR_DateFormat == 3) {
        echo ("return(date = year + '/' + str_month + '/' + str_day");
      }

      echo (" + ' ' + str_hours + ':' + str_minutes + ':' + str_seconds);");

      ?>

    }

    function updateStatus(host, status){  // Updates the status of the host informed and apply the animations

      if(status == 0){

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo ('
              if(hostStatus[host].status == 1){
                var newUp = ("<div style=\"font-size:'. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000 ;margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $warning .'\" → \"'. $up .'\"</div>");
              }
              else if(hostStatus[host].status == 2){
                var newUp = ("<div style=\"font-size:'. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000 ;margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $critical .'\" → \"'. $up .'\"</div>");
              }
              else if(hostStatus[host].status == 3){
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $down .'\" → \"' .$up. '\"</div>");
              }
              else{
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $unknown .'\" → \"' .$up. '\"</div>");
              }
              ');
          }
        }
        ?>

        hostStatus[host].status = status;

        <?php
        if($nagMapR_Lines == 1){
          echo ('
            if(Array.isArray(hostStatus[host].parents)){
              for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
                for (var ii = LINES.length - 1; ii >= 0; ii--) {
                  if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
                    LINES[ii].line.setOptions({strokeColor: "#59BB48"});
                }          
              }
            }
            '."\n");
        }
        ?>

        MARK[host].setIcon(iconGreen);
        MARK[host].setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(function () {MARK[host].setAnimation(null);}, 500);
        MARK[host].setZIndex(2);
        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1)
            echo ('newDivs = newUp.concat(newDivs);');
        }
        ?>

      }else if (status == 1) {

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo (' 
              if(hostStatus[host].status == 0)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $up .'\" → \"'. $warning .'\"</div>");
              else if(hostStatus[host].status == 2)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $critical .'\" → \"'. $warning .'\"</div>");
              else if(hostStatus[host].status == 3)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $down .'\" → \"'. $warning .'\"</div>");
              else
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $unknown .'\" → \"'. $warning .'\"</div>");
              ');
          }
        }
        ?>

        <?php
        if($nagMapR_Lines == 1)
          echo ('
            if(Array.isArray(hostStatus[host].parents)){
              for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
                for (var ii = LINES.length - 1; ii >= 0; ii--) {
                  if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
                    LINES[ii].line.setOptions({strokeColor: "#ffff00"});
                }          
              }
            }
            '."\n");
        ?>

        MARK[host].setIcon(iconYellow);
        MARK[host].setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(function () {MARK[host].setAnimation(null);}, 500);
        MARK[host].setZIndex(3);
        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1)
            echo ('newDivs = newUp.concat(newDivs);');
        }
        ?>

        hostStatus[host].status = status;
      }else if (status == 2) {

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo ('
              if(hostStatus[host].status == 0)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#ff6a00; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $up .'\" → \"'. $critical .'\"</div>");
              else if(hostStatus[host].status == 1)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#ff6a00; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $warning .'\" → \"'. $critical .'\"</div>");
              else if(hostStatus[host].status == 3)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#ff6a00; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $down .'\" → \"'. $critical .'\"</div>");
              else
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#ff6a00; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $unknown .'\" → \"'. $critical .'\"</div>");
              ');
          }
        }
        ?>

        <?php
        if($nagMapR_Lines == 1)
          echo ('
            if(Array.isArray(hostStatus[host].parents)){
              for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
                for (var ii = LINES.length - 1; ii >= 0; ii--) {
                  if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
                    LINES[ii].line.setOptions({strokeColor: "#ff6a00"});
                }          
              }
            }
            '."\n");
        ?>

        MARK[host].setIcon(iconOrange);
        MARK[host].setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(function () {MARK[host].setAnimation(null);}, 500);
        MARK[host].setZIndex(3);
        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1)
            echo ('newDivs = newUp.concat(newDivs);');
        }
        ?>

        hostStatus[host].status = status;
      } else if (status == 3) {

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo (' 
              if(hostStatus[host].status == 0)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#770101; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $up .'\" → \"'. $down .'\"</div>");
              else if(hostStatus[host].status == 1)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#770101; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $warning .'\" → \"'. $down .'\"</div>");
              else if(hostStatus[host].status == 2)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#770101; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $critical .'\" → \"'. $down .'\"</div>");
              else
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#770101; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $unknown .'\" → \"'. $down .'\"</div>");
              ');
          }
        }
        ?>

        hostStatus[host].status = status;

        <?php
        if($nagMapR_Lines == 1){
          echo ('
            if(Array.isArray(hostStatus[host].parents)){
              for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
                for (var ii = LINES.length - 1; ii >= 0; ii--) {
                  if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
                    LINES[ii].line.setOptions({strokeColor: "#ff0000"});
                }          
              }
            }
            '."\n");
        }
        ?>

        MARK[host].setIcon(iconRed);
        MARK[host].setAnimation(google.maps.Animation.BOUNCE);

        <?php
        if($nagMapR_PlaySound ==1)
          echo ("audio.play();")
        ?>

        setTimeout(function () {MARK[host].setAnimation(null);}, 15000);
        MARK[host].setZIndex(4);

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1)
            echo ('newDivs = newUp.concat(newDivs);');
        }
        ?>

      } else {
        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo (' 
              if(hostStatus[host].status == 0)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#A9ABAE; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $up .'\" → \"'. $unknown .'\"</div>");
              else if(hostStatus[host].status == 1)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#A9ABAE; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $warning .'\" → \"'. $unknown .'\"</div>");
              else if(hostStatus[host].status == 2)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#A9ABAE; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $critical .'\" → \"'. $unknown .'\"</div>");
              else if(hostStatus[host].status == 3)
                var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#A9ABAE; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $down .'\" → \"'. $unknown .'\"</div>");
              ');
          }
        }
        ?>

        hostStatus[host].status = status;

        <?php
        if($nagMapR_Lines == 1)
          echo ('
            if(Array.isArray(hostStatus[host].parents)){
              for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
                for (var ii = LINES.length - 1; ii >= 0; ii--) {
                  if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
                    LINES[ii].line.setOptions({strokeColor: "#A9ABAE"});
                }          
              }
            }
            '."\n");
        ?>

        MARK[host].setIcon(iconGrey);
        MARK[host].setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(function () {MARK[host].setAnimation(null);}, 500);
        MARK[host].setZIndex(3);
        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1)
            echo ('newDivs = newUp.concat(newDivs);');
        }
        ?>
      }

    }
    <?php
    if($nagMapR_ChangesBar == 1){
      if($nagMapR_ChangesBarMode == 1){
        echo ("var newDivs = \"\";");
      }

      if($nagMapR_ChangesBarMode == 2){
        echo('

          function addHost(i, status, time){
            if(status ==  "WAR"){
              document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + hostStatus[i].nagios_host_name + "-WAR\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + time + "'. $timeSuffix .'</div>");
              var div = document.getElementById(hostStatus[i].nagios_host_name+"-WAR");
            }
            if(status ==  "CRIT"){
              document.getElementById(\'critHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + hostStatus[i].nagios_host_name + "-CRIT\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#ff6a00; color:white; vertical-align: middle; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + time + "'. $timeSuffix .'</div>");
              var div = document.getElementById(hostStatus[i].nagios_host_name+"-CRIT");
            }
            if(status ==  "DOWN"){
              document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + hostStatus[i].nagios_host_name + "-DOWN\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#770101; color:white; vertical-align: middle; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + time + "'. $timeSuffix .'</div>");
              var div = document.getElementById(hostStatus[i].nagios_host_name+"-DOWN");
            }
            setTimeout( function (){
              div.style.maxHeight =(' . $nagMapR_FontSize . ' + 4 ) * 2;
              div.style.opacity = "1";
            }, 80);

          }

          function removeHost(i, status){

            if(status == "WAR"){
              var parent = document.getElementById("warHosts");
              var child = document.getElementById(hostStatus[i].nagios_host_name+"-WAR");
            }
            if(status == "CRIT"){
              var parent = document.getElementById("critHosts");
              var child = document.getElementById(hostStatus[i].nagios_host_name+"-CRIT");
            }
            if(status == "DOWN"){
              var parent = document.getElementById("downHosts");
              var child = document.getElementById(hostStatus[i].nagios_host_name+"-DOWN");
            }
            child.style.maxHeight = 0;
            child.style.opacity = 0;

            setTimeout( function() {parent.removeChild(child);}, 900);

          }

          for (var i = 0; i < hostStatusPre.length; i++) {
            var name = hostStatusPre[i].nagios_host_name;
            if(hostStatusPre[i].status == 1){
              document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + name + "-WAR\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + hostStatusPre[i].alias + " - ('. $waiting .')</div>");
            }
            if(hostStatusPre[i].status == 2){
              document.getElementById(\'critHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + name + "-CRIT\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#ff6a00; color:white; vertical-align: middle;\">" + hostStatusPre[i].alias + " - ('. $waiting .')</div>");
            }
            if(hostStatusPre[i].status == 3){
              document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + name + "-DOWN\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#770101; color:white; vertical-align: middle;\">" + hostStatusPre[i].alias + " - ('. $waiting .')</div>");
            }
          }
          ');
}
}
?>

  setInterval(function(){ // Request the arrau with the update status of each host.

    var ajax = new XMLHttpRequest();

    var arrayHosts;

    ajax.open('POST', 'update.php?key=<?php echo $nagMapR_key ?>', true);

    ajax.send();

    ajax.onreadystatechange = function(){

      if(ajax.readyState == 4 && ajax.status == 200) {
        arrayHosts = JSON.parse(ajax.responseText); 

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo ('
              newDivs = "";
              var qntChange = 0; 
              ');
          }
        }
        ?>

        for (var i = 0; i < hostStatus.length; i++) {
        if(hostStatus[i].status != arrayHosts[hostStatus[i].nagios_host_name].status){ //Call the update function if the last status is different from the current status.'

          <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo ('
              qntChange++; 
              ');
          }
          if($nagMapR_ChangesBarMode == 2){
            echo('
              if(hostStatus[i].status == 1)
                removeHost(i, "WAR");
              if(hostStatus[i].status == 2)
                removeHost(i, "CRIT");
              if(hostStatus[i].status == 3){
                toastr["success"](hostStatus[i].alias);
                removeHost(i, "DOWN");
              }
              ');
          }
        }
        ?>
        updateStatus(i, arrayHosts[hostStatus[i].nagios_host_name].status);

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo ('
          }
        }

        if(qntChange > 0){
          var n = now();
          document.getElementById("changesbar").innerHTML = "<div class=\'news\' id=\'news-" + n + "\' style=\'opacity:0; max-height: 0px;\'>" + newDivs + "</div>" + document.getElementById("changesbar").innerHTML;
          setTimeout(function(){
            document.getElementById("news-" + n).style.maxHeight =(' . $nagMapR_FontSize . ' + 4 ) * 2 * qntChange;
            document.getElementById("news-" + n).style.opacity = "1";
          }, 80);
        }
        '."\n");
          }
          if($nagMapR_ChangesBarMode == 2){
            echo('
              if(arrayHosts[hostStatus[i].nagios_host_name].status == 1){
                addHost(i, "WAR", arrayHosts[hostStatus[i].nagios_host_name].time);
              }
              if(arrayHosts[hostStatus[i].nagios_host_name].status == 2){
                addHost(i, "CRIT", arrayHosts[hostStatus[i].nagios_host_name].time);
              }
              if(arrayHosts[hostStatus[i].nagios_host_name].status == 3){
                addHost(i, "DOWN", arrayHosts[hostStatus[i].nagios_host_name].time);
              }                  
            }
            else{
              if(hostStatus[i].status == 1)
                document.getElementById(hostStatus[i].nagios_host_name+"-WAR").innerHTML = hostStatus[i].alias + " - '. $timePrefix .'" + arrayHosts[hostStatus[i].nagios_host_name].time + "'. $timeSuffix .'";
              if(hostStatus[i].status == 2)
                document.getElementById(hostStatus[i].nagios_host_name+"-CRIT").innerHTML = hostStatus[i].alias + " - '. $timePrefix .'" + arrayHosts[hostStatus[i].nagios_host_name].time + "'. $timeSuffix .'";
              if(hostStatus[i].status == 3)
                document.getElementById(hostStatus[i].nagios_host_name+"-DOWN").innerHTML = hostStatus[i].alias + " - '. $timePrefix .'" + arrayHosts[hostStatus[i].nagios_host_name].time + "'. $timeSuffix .'";                    
            }
          }
          '."\n");
          }
        }
        else
          echo('
        }
      }
      '); 
        ?>
      }
    };
  }, <?php echo $nagMapR_TimeUpdate; ?>000);

//Modal functions

if(<?php if($nagMapR_version != $nagMapR_CurrVersion) echo 'true'; else echo 'false'; ?>)
{
  document.getElementById('myModal').style.display = "block";
  setTimeout( function(){
    document.getElementById('myModal').style.opacity = 1;
    document.getElementById('modalContent').style.top = "10%";
  },100);
}

document.getElementById("closeModal").onclick = function() {
  document.getElementById('modalContent').style.top = "-300px";
  document.getElementById('myModal').style.opacity = 0;
  setTimeout( function(){
    document.getElementById('myModal').style.display = "none";
  },550);
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == document.getElementById('myModal')) {
    document.getElementById('modalContent').style.top = "-300px";
    document.getElementById('myModal').style.opacity = 0;
    setTimeout( function(){
      document.getElementById('myModal').style.display = "none";
    },550);
  }
}

<?php 
if($nagMapR_Debug == 1)
{
  echo('
    window.onerror = function (msg, url, lineNo, columnNo, error) {
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "20000",
        "extendedTimeOut": "10000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      };

      toastr["error"]("'. $message .' " + msg + " - '. $error .': " + error + " - URL: " + url + " - '. $lineNum .' " + lineNo + " - '. $at .' " + now(),"'. $error .'");

      toastr.options = {
        "closeButton": false,
        "progressBar": false,
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000"
      };
      ');
  if($nagMapR_Reporting == 1)
    echo ('
      var report = "err:" + error + ",url:" + url + ",lineN:" + lineNo + ",at:" + now() + ",hFr:['. $nagMapR_FilterHostgroup. '],sF:['. $nagMapR_FilterService. '],uD:['. $nagMapR_Debug. '],iN:['. $nagMapR_IsNagios. '],uS:['. $nagMapR_Style. '],uCB:['. $nagMapR_ChangesBar. '],mCB:['. $nagMapR_ChangesBarMode. '],dF:['. $nagMapR_DateFormat. '],sL:['. $nagMapR_Lines. '],tU:['. $nagMapR_TimeUpdate. ']";

      $.post( "https://nagmaprebornanalytics.000webhostapp.com/report/", {report:Encrypt(report)}, function( data ) {});

      toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
      };

      toastr["success"]("'. $errorFound .'", "'. $error . $reported .'");

      toastr.options = {
        "closeButton": false,
        "progressBar": false,
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000"
      };

    }
    ');
  else
    echo ('}');
}
else{
  if($nagMapR_Reporting == 1)
    echo ('
      window.onerror = function (msg, url, lineNo, columnNo, error) {

        var report = "err:" + error + ",url:" + url + ",lineN:" + lineNo + ",at:" + now() + ",hFr:['. $nagMapR_FilterHostgroup. '],sF:['. $nagMapR_FilterService. '],uD:['. $nagMapR_Debug. '],iN:['. $nagMapR_IsNagios. '],uS:['. $nagMapR_Style. '],uCB:['. $nagMapR_ChangesBar. '],mCB:['. $nagMapR_ChangesBarMode. '],dF:['. $nagMapR_DateFormat. '],sL:['. $nagMapR_Lines. '],tU:['. $nagMapR_TimeUpdate. ']";

        $.post( "https://nagmaprebornanalytics.000webhostapp.com/report/", {report:Encrypt(report)}, function( data ) {});

        toastr.options = {
          "closeButton": true,
          "debug": false,
          "newestOnTop": false,
          "progressBar": true,
          "positionClass": "toast-top-right",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "10000",
          "extendedTimeOut": "5000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        };

        toastr["success"]("'. $errorFound .'", "'. $error . $reported .'");

        toastr.options = {
          "closeButton": false,
          "progressBar": false,
          "preventDuplicates": true,
          "onclick": null,
          "showDuration": "1000",
          "timeOut": "10000",
          "extendedTimeOut": "1000"
        };
      }
      ');
}


?>
</script>
<script src="debugInfo/resources/js/jquery.min.js"></script>
<script src="resources/toastr/toastr.min.js"></script>
<?php
if($nagMapR_Reporting == 1) // Used for encryption
echo('
  <script type="text/javascript" src="resources/BigInt.js"></script>

  <script type="text/javascript" src="resources/Barrett.js"></script>

  <script type="text/javascript" src="resources/RSA_Stripped.js"></script>
  ');
  ?>
  <script type="text/javascript">
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-right",
      "preventDuplicates": true,
      "onclick": null,
      "showDuration": "1000",
      "hideDuration": "1000",
      "timeOut": "10000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    };
  </script>
  <?php
  if($nagMapR_Reporting == 1)
    echo('
      <script type="text/javascript">
      var key;

      setMaxDigits(262);
      key = new RSAKeyPair(
      "10001",
      "10001",
      "B5A9FB6760A92AD48D2C28572FE07BCA57E73F50F2E2591ED7350AB7F68F432E4889002019091E0F37F8C7C4D2D0EA401A2E6C24008382FA66D56E1FB813E21505BC2D41A6BFCF45CC59C6F9B98BCE36CFE9E543F6149D7EE708D9489BF6E414603021B3083C71DA22AF03C0038B40EAAE82B4489AEBB299744A0F60797FA052D0715F20F6247957D8B706DB14B14C7DDC9698D76376348C43D1E30ADF054A6AFBCB58C65EBD351F3B4154D57605529F92C56265C380382F369D6C31023825FA56892EC6C969C62D94E506B5DE8D7E88040052DF518690B606F4E76D2F15DD072B28AABCD2FAE113C9E1B160CBCCAE73B96041365E26E8634A99E751916E7A3B",
      2048
      );

      function Encrypt(data)
      {
        var ciphertext = encryptedString(key, data,
        RSAAPP.PKCS1Padding, RSAAPP.RawEncoding);
        return window.btoa(ciphertext);
      };

      var _paq = _paq || [];
      _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
      _paq.push(["setDomains", ["*."]]);
      _paq.push(["trackPageView"]);
      _paq.push(["enableLinkTracking"]);
      (function() {
        var u="//nagmaprebornanalytics.000webhostapp.com/";
        _paq.push(["setTrackerUrl", u+"piwik.php"]);
        _paq.push(["setSiteId", "2"]);
        var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];
        g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
        })();
        </script>
        ');
        ?>
      </body>
      </html>
