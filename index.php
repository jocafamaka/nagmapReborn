<?php
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
$page = $_SERVER['PHP_SELF'];
$nagMapR_version = '1.0';
include('config.php');

// Check if the translation file informed exist.
if(file_exists("langs/$nagMapR_Lang.php"))
  include("langs/$nagMapR_Lang.php");
else
  die("$nagMapR_Lang.php does not exist in the languages folder! Please set the proper \$nagMapR_Lang variable in NagMap Reborn config file!");

// Validation of configuration variables.
if(!is_string($nagios_cfg_file)) 
  die("\$nagios_cfg_file $var_cfg_error");

if(!is_string($nagios_status_dat_file)) 
  die("\$nagios_status_dat_file $var_cfg_error");

if(!is_string($nagMapR_FilterHostgroup)) 
  die("\$nagMapR_FilterHostgroup $var_cfg_error");

if(!is_string($nagMapR_MapCentre)) 
  die("\$nagMapR_MapCentre $var_cfg_error");

if(!is_string($nagMapR_MapType)) 
  die("\$nagMapR_MapType $var_cfg_error");

if(!is_string($nagMapR_key)) 
  die("\$nagMapR_key $var_cfg_error");

if(!( is_float($nagMapR_Debug) || is_int($nagMapR_Debug) ))
  die("\$nagMapR_Debug $var_cfg_error");

if(!( is_float($nagMapR_MapZoom) || is_int($nagMapR_MapZoom) ))
  die("\$nagMapR_MapZoom $var_cfg_error");

if(!( is_float($nagMapR_ChangesBar) || is_int($nagMapR_ChangesBar) ))
  die("\$nagMapR_ChangesBar $var_cfg_error");

if(!( is_float($nagMapR_ChangesBarSize) || is_int($nagMapR_ChangesBarSize) ))
  die("\$nagMapR_ChangesBarSize $var_cfg_error");

if(!( is_float($nagMapR_FontSize) || is_int($nagMapR_FontSize) ))
  die("\$nagMapR_FontSize $var_cfg_error");

if(!( is_float($nagMapR_DateFormat) || is_int($nagMapR_DateFormat) ))
  die("\$nagMapR_DateFormat $var_cfg_error");

if(!( is_float($nagMapR_PlaySound) || is_int($nagMapR_PlaySound) ))
  die("\$nagMapR_PlaySound $var_cfg_error");

if(!( is_float($nagMapR_TimeUpdate) || is_int($nagMapR_TimeUpdate) ))
  die("\$nagMapR_TimeUpdate $var_cfg_error");


include('marker.php');

if ($javascript == "") {
  echo $no_data_error;
  die("ERROR");
}

?>
<html>
<head>
  <link rel="shortcut icon" href="icons/favicon.ico" />
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link rel=StyleSheet href="style.css" type="text/css" media=screen>
  <title>NagMap Reborn <?php echo $nagMapR_version ?></title>
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <script type="text/javascript">

    // Defines the array used for the comparisons.
    var hostStatus = <?php echo json_encode($jsData); ?>;

    //Defines the array that will contain the makrs.
    var MARK = [];

    //Defines the array that will contain the Polylines.
    var LINES = [];

    //Define the source of the audio file.
    <?php
    if($nagMapR_PlaySound ==1)
      echo ("var audio = new Audio('Beep.mp3');")
    ?>

    //static code from index.pnp
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
      window.map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

// generating dynamic code from here
// if the page ends here, there is something seriously wrong, please contact maco@blava.net for help

<?php
  // print the body of the page here
echo $linesArray;
echo ("\n\n");
echo $javascript;
  echo '};'; //end of initialize function
  echo '
  </script>
  </head>
  <body style="margin:0px; padding:0px;" onload="initialize()">';
  if ($nagMapR_ChangesBar == '1') {
    echo '<div id="map_canvas" style="width:100%; height:'.(100-$nagMapR_ChangesBarSize).'%; float: left"></div>';
    echo '<div id="changesbar" style="border-color:black;  border-top:2px; padding-left: 1px; background: black; height:'.$nagMapR_ChangesBarSize.'%; overflow:auto;"></div>';
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
      if(hostStatus[host].status == 1)
        var newUp = ("<div style=\"font-size: <?php echo $nagMapR_FontSize; ?>px; text-shadow:2px 2px 4px #000000 ;margin-bottom:1px; font-weight:bold; padding-left:10px; width:100%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"<?php echo $warning; ?>\" -> \"<?php echo $up; ?>\"</div>");
      if(hostStatus[host].status == 2)
        var newUp = ("<div style=\"font-size: <?php echo $nagMapR_FontSize; ?>px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:10px; width:100%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"<?php echo $down; ?>\" -> \"<?php echo $up; ?>\"</div>");
      hostStatus[host].status = status;
      for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
        //console.log("ArrayHost: " + hostStatus[host].host_name + " | LinesHost:" + LINES[host-1].host + " | ArrayParent: " + hostStatus[host].parents[i] + " | LinesParent: " + LINES[host-1].parent);
        for (var ii = LINES.length - 1; ii >= 0; ii--) {
          if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
            LINES[ii].line.setOptions({strokeColor: '#59BB48'});
        }          
      }
      MARK[host].setIcon('http://www.google.com/mapfiles/marker_green.png');
      MARK[host].setAnimation(google.maps.Animation.BOUNCE);
      setTimeout(function () {MARK[host].setAnimation(null);}, 500);
      MARK[host].setZIndex(2);
      <?php
      if($nagMapR_ChangesBar == '1')
        echo ('document.getElementById("changesbar").innerHTML = newUp + document.getElementById("changesbar").innerHTML;');
      ?>
    }else if (status == 1) {

      if(hostStatus[host].status == 0)
        var newUp = ("<div style=\"font-size: <?php echo $nagMapR_FontSize; ?>px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:10px; width:100%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"<?php echo $up; ?>\" -> \"<?php echo $warning; ?>\"</div>");
      if(hostStatus[host].status == 2)
        var newUp = ("<div style=\"font-size: <?php echo $nagMapR_FontSize; ?>px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:10px; width:100%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"<?php echo $down; ?>\" -> \"<?php echo $warning; ?>\"</div>");
      hostStatus[host].status = status;
      for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
        //console.log("ArrayHost: " + hostStatus[host].host_name + " | LinesHost:" + LINES[host-1].host + " | ArrayParent: " + hostStatus[host].parents[i] + " | LinesParent: " + LINES[host-1].parent);
        for (var ii = LINES.length - 1; ii >= 0; ii--) {
          if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
            LINES[ii].line.setOptions({strokeColor: '#ffff00'});
        }          
      }
      MARK[host].setIcon('http://www.google.com/mapfiles/marker_yellow.png');
      MARK[host].setAnimation(google.maps.Animation.BOUNCE);
      setTimeout(function () {MARK[host].setAnimation(null);}, 500);
      MARK[host].setZIndex(3);
      <?php
      if($nagMapR_ChangesBar == '1')
        echo ('document.getElementById("changesbar").innerHTML = newUp + document.getElementById("changesbar").innerHTML;');
      ?>
    } else if (status == 2) {

      if(hostStatus[host].status == 0)
        var newUp = ("<div style=\"font-size: <?php echo $nagMapR_FontSize; ?>px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:10px; width:100%; background:#b30606; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"<?php echo $up; ?>\" -> \"<?php echo $down; ?>\"</div>");
      if(hostStatus[host].status == 1)
        var newUp = ("<div style=\"font-size: <?php echo $nagMapR_FontSize; ?>px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:10px; width:100%; background:#b30606; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"<?php echo $warning; ?>\" -> \"<?php echo $down; ?>\"</div>");
      hostStatus[host].status = status;
      for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
        //console.log("ArrayHost: " + hostStatus[host].host_name + " | LinesHost:" + LINES[host-1].host + " | ArrayParent: " + hostStatus[host].parents[i] + " | LinesParent: " + LINES[host-1].parent);
        for (var ii = LINES.length - 1; ii >= 0; ii--) {
          if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
            LINES[ii].line.setOptions({strokeColor: '#ff0000'});
        }          
      }
      MARK[host].setIcon('http://www.google.com/mapfiles/marker.png');
      MARK[host].setAnimation(google.maps.Animation.BOUNCE);
      <?php
      if($nagMapR_PlaySound ==1)
        echo ("audio.play();")
      ?>
      setTimeout(function () {MARK[host].setAnimation(null);}, 15000);
      MARK[host].setZIndex(4);
      <?php
      if($nagMapR_ChangesBar == '1')
        echo ('document.getElementById("changesbar").innerHTML = newUp + document.getElementById("changesbar").innerHTML;');
      ?>
    }

  }

  setInterval(function(){ // Request the arrau with the update status of each host.

    var ajax = new XMLHttpRequest();

    var ArrayHosts;

    ajax.open('POST', 'update.php?key=<?php echo $nagMapR_key ?>', true);

    ajax.send();

    ajax.onreadystatechange = function(){

      if(ajax.readyState == 4 && ajax.status == 200) {
        ArrayHosts = JSON.parse(ajax.responseText);

        for (var i = hostStatus.length - 1; i >= 0; i--) {
          if(hostStatus[i].status != ArrayHosts[hostStatus[i].nagios_host_name].status) //Call the update function if the last status is different from the current status.
            updateStatus(i, ArrayHosts[hostStatus[i].nagios_host_name].status);
            //console.log("ARRAY1 = NAME: " + hostStatus[i].nagios_host_name +" STATUS: " + hostStatus[i].status + " | ARRAY2 = " + ArrayHosts[hostStatus[i].nagios_host_name].status + "    - " + i);
          }

        }
      };
    }, <?php echo $nagMapR_TimeUpdate; ?>000);

  </script>

  <!-- <iframe frameborder="1" marginwidth="0" marginheight="0" style= "position: fixed; top: 2px; left:2px ; height: 34%;width: 43%;" src="FRAME/index.php"></iframe> -->

</body>
</html>
