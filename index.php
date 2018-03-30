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
$nagMapR_version = '1.2.1';
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

if(!is_int($nagMapR_Debug))
  die("\$nagMapR_Debug $var_cfg_error");

if(!is_int($nagMapR_DateFormat))
  die("\$nagMapR_DateFormat $var_cfg_error");

if(!is_int($nagMapR_PlaySound))
  die("\$nagMapR_PlaySound $var_cfg_error");

if(!is_int($nagMapR_ChangesBar))
  die("\$nagMapR_ChangesBar $var_cfg_error");

if(($nagMapR_ChangesBarMode < 1) || ($nagMapR_ChangesBarMode > 2))
  die("\$nagMapR_ChangesBarMode $var_cfg_error");

if(!is_int($nagMapR_Lines))
  die("\$nagMapR_Lines $var_cfg_error");

if(!( is_float($nagMapR_ChangesBarSize) || is_int($nagMapR_ChangesBarSize) ))
  die("\$nagMapR_ChangesBarSize $var_cfg_error");

if(!( is_float($nagMapR_FontSize) || is_int($nagMapR_FontSize) ))
  die("\$nagMapR_FontSize $var_cfg_error");

if(!( is_float($nagMapR_MapZoom) || is_int($nagMapR_MapZoom) ))
  die("\$nagMapR_MapZoom $var_cfg_error");

if(!( is_float($nagMapR_TimeUpdate) || is_int($nagMapR_TimeUpdate) ))
  die("\$nagMapR_TimeUpdate $var_cfg_error");

if($nagMapR_IconStyle > 2 || $nagMapR_IconStyle < 0)
  die("\$nagMapR_IconStyle $var_cfg_error");

include('marker.php');

if ($javascript == "") {
  echo $no_data_error;
  die("ERROR");
}

?>
<html>
<head>
  <link rel="shortcut icon" href="icons/NagFavIcon.ico" />
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link rel=StyleSheet href="style.css" type="text/css" media=screen>
  <title>NagMap Reborn <?php echo $nagMapR_version ?></title>
  <script src="http://maps.google.com/maps/api/js" type="text/javascript"></script>
  <script type="text/javascript">

    // Defines the array used for the comparisons.
    var hostStatus = <?php echo json_encode($jsData); ?>;

    //Defines the array that will contain the marks.
    var MARK = [];

    //Defines the array that will contain the Polylines.
    var LINES = [];

    //Define the source of the audio file.
    <?php
    if($nagMapR_PlaySound ==1)
      echo ("var audio = new Audio('Beep.mp3');\n")
    ?>

    //Define icons style.
    switch(<?php echo $nagMapR_IconStyle; ?>){
      case 0:
      var iconRed = {
        url: 'icons/MarkerRedStyle0.png',
        size: new google.maps.Size(20, 34),
        anchor: new google.maps.Point(10, 33)
      };

      var iconGreen = {
        url: 'icons/MarkerGreenStyle0.png',
        size: new google.maps.Size(20, 34),
        anchor: new google.maps.Point(10, 33)
      };

      var iconYellow = {
        url: 'icons/MarkerYellowStyle0.png',
        size: new google.maps.Size(20, 34),
        anchor: new google.maps.Point(10, 33)
      };
      break;

      case 1:
      var iconRed = {
        url: 'icons/MarkerRedStyle1.png',
        size: new google.maps.Size(28, 42),
        anchor: new google.maps.Point(12, 39)
      };

      var iconGreen = {
        url: 'icons/MarkerGreenStyle1.png',
        size: new google.maps.Size(28, 42),
        anchor: new google.maps.Point(13, 39)
      };

      var iconYellow = {
        url: 'icons/MarkerYellowStyle1.png',
        size: new google.maps.Size(28, 42),
        anchor: new google.maps.Point(12, 39)
      };
      break;

      case 2:
      var iconRed = {
        url: 'icons/MarkerRedStyle2.png',
        size: new google.maps.Size(29, 44),
        anchor: new google.maps.Point(14, 43)
      };

      var iconGreen = {
        url: 'icons/MarkerGreenStyle2.png',
        size: new google.maps.Size(29, 44),
        anchor: new google.maps.Point(13, 42)
      };

      var iconYellow = {
        url: 'icons/MarkerYellowStyle2.png',
        size: new google.maps.Size(29, 42),
        anchor: new google.maps.Point(13, 41)
      };
      break;
    }

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
      window.map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

// generating dynamic code from here
// if the page ends here, there is something seriously wrong, please contact maco@blava.net for help

//Filling array of Polylines
<?php
  // print the body of the page here
echo $linesArray;
echo ("\n\n");
echo $javascript;
  echo '};'; //end of initialize function
  echo '
  </script>
  </head>
  <body style="margin:0px; padding:0px; overflow:hidden;" onload="initialize()">';
  if ($nagMapR_Debug == 1){
    echo ('<div id="div_fixa" class="div_fixa" style="z-index:2000;"><a href="debugInfo/index.php"><button class="button" style="vertical-align:middle"><span>Debug page</span></button></a></div>');
  }
  if ($nagMapR_ChangesBar == 1) {
    echo '<div id="map_canvas" style="width:100%; height:'.(100-$nagMapR_ChangesBarSize).'%; float: left"></div>';
    echo '<div id="changesbar" style="padding-top:2px; padding-left: 1px; background: black; height:'.$nagMapR_ChangesBarSize.'%; overflow:auto;">';
    if($nagMapR_ChangesBarMode == 2){
      echo('<div id="downHosts"></div><div id="warHosts"></div>');
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
            if(hostStatus[host].status == 1)
              var newUp = ("<div style=\"font-size:'. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000 ;margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $warning .'\" -> \"'. $up .'\"</div>");
            if(hostStatus[host].status == 2)
              var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#159415; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $down .'\" -> \"' .$up. '\"</div>");
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
              var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $up .'\" -> \"'. $warning .'\"</div>");
            if(hostStatus[host].status == 2)
              var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $down .'\" -> \"'. $warning .'\"</div>");
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

    } else if (status == 2) {

      <?php
      if($nagMapR_ChangesBar == 1){
        if($nagMapR_ChangesBarMode == 1){
          echo (' 
            if(hostStatus[host].status == 0)
              var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#b30606; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $up .'\" -> \"'. $down .'\"</div>");
            if(hostStatus[host].status == 1)
              var newUp = ("<div style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#b30606; color:white; vertical-align: middle;\">" + now() + " - " + hostStatus[host].alias + ": \"'. $warning .'\" -> \"'. $down .'\"</div>");
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

    }

  }
  <?php
  if($nagMapR_ChangesBar == 1){
    if($nagMapR_ChangesBarMode == 1){
      echo ("var newDivs = \"\";");
    }

    if($nagMapR_ChangesBarMode == 2){
      echo('
        for (var i = 0; i < hostStatus.length; i++) {
          var name = hostStatus[i].nagios_host_name;
          if(hostStatus[i].status == 1){
            document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + name + "\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle;\">" + hostStatus[i].alias + " - ('. $waiting .')</div>");
          }
          if(hostStatus[i].status == 2){
            document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + name + "\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#b30606; color:white; vertical-align: middle;\">" + hostStatus[i].alias + " - ('. $waiting .')</div>");
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
                if((hostStatus[i].status == 1 || hostStatus[i].status == 2)){
                  if(hostStatus[i].status == 1)
                    var parent = document.getElementById("warHosts");
                  if(hostStatus[i].status == 2)
                    var parent = document.getElementById("downHosts");
                  var child = document.getElementById(hostStatus[i].nagios_host_name);
                  child.style.opacity = 0;
                  child.style.maxHeight = 0;
                  parent.removeChild(child);
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
              document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + hostStatus[i].nagios_host_name + "\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#c5d200; color:white; vertical-align: middle; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + arrayHosts[hostStatus[i].nagios_host_name].last_state_change + "'. $timeSuffix .'</div>");
              document.getElementById(hostStatus[i].nagios_host_name).style.maxHeight =(' . $nagMapR_FontSize . ' + 4 ) * 2;
              document.getElementById(hostStatus[i].nagios_host_name).style.opacity = "1";
            }
            if(arrayHosts[hostStatus[i].nagios_host_name].status == 2){
              document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", "<div class=\"news\" id=\"" + hostStatus[i].nagios_host_name + "\" style=\"font-size: '. $nagMapR_FontSize .'px; text-shadow:2px 2px 4px #000000; margin-bottom:1px; font-weight:bold; padding-left:1%; width:99%; background:#b30606; color:white; vertical-align: middle; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + arrayHosts[hostStatus[i].nagios_host_name].last_state_change + "'. $timeSuffix .'</div>");
              document.getElementById(hostStatus[i].nagios_host_name).style.maxHeight =(' . $nagMapR_FontSize . ' + 4 ) * 2;
              document.getElementById(hostStatus[i].nagios_host_name).style.opacity = "1";
            }                  
          }
          else{
            if(hostStatus[i].status == 1 || hostStatus[i].status == 2){
              document.getElementById(hostStatus[i].nagios_host_name).innerHTML = hostStatus[i].alias + " - '. $timePrefix .'" + arrayHosts[hostStatus[i].nagios_host_name].last_state_change + "'. $timeSuffix .'";
            }
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

</script>
</body>
</html>
