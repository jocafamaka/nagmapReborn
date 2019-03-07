<?php
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
include_once('validateAndVerify.php');

$nagMapR_version = file_get_contents('VERSION');
$nagMapR_CurrVersion = file_get_contents('https://raw.githubusercontent.com/jocafamaka/nagmapReborn/master/VERSION'); //Get last stable version known.
if($nagMapR_CurrVersion == "")  //Set local version in case of fail.
$nagMapR_CurrVersion = $nagMapR_version;

if($nagMapR_Reporting == 1){
  $nagMapR_Domain = file_get_contents('https://raw.githubusercontent.com/jocafamaka/nagmapReborn/developing/resources/reporter/DOMAIN'); //Get last online domain known.
  if($nagMapR_Domain == "")  //Set local domain in case of fail.
  $nagMapR_Domain = file_get_contents('resources/reporter/DOMAIN');
}

include_once('marker.php');

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
  <link rel=StyleSheet href="resources/style.css?v=1.6.4" type="text/css" media=screen>
  <link href="resources/toastr/toastr.css" rel="stylesheet"/>
  <link href="resources/sa/sweetalert2.min.css" rel="stylesheet"/>
  <title>NagMap Reborn <?php echo $nagMapR_version ?></title>
  <?php
  if($nagMapR_MapAPI == 0){
    echo ('<script src="//maps.google.com/maps/api/js?key='.$nagMapR_Mapkey.'" type="text/javascript"></script>');
  }
  else{
    echo ('
      <link rel="stylesheet" href="resources/leaflet/leaflet.css" />
      <script type="text/javascript" src="resources/leaflet/leaflet.js"></script>
      <script type="text/javascript" src="resources/leaflet/oms.js"></script>
      <script type="text/javascript" src="resources/leaflet/leaflet.smoothmarkerbouncing.js"></script>
      ');
  }
  ?> 

  <script type="text/javascript">

    // Defines the array used for the comparisons.
    var hostStatus = <?php echo json_encode($jsData); ?>;

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3 && $nagMapR_ChangesBarMode == 2)
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

    <?php
    if($nagMapR_MapAPI == 0){
      echo("
        //Defines the array that will contain the infowindows of the marks.
        var INFO = [];

        //Used for infowindows control.
        clicked = false;
        ");
    }

    if($nagMapR_PlaySound == 1)
      echo ("//Define the source of the audio file.
        var audio = new Audio('resources/Beep.mp3');\n");

    if($nagMapR_MapAPI == 0){
      echo("
        var iconRed = {
          url: 'resources/img/icons/MarkerRedSt-".$nagMapR_IconStyle.".png',
          size: new google.maps.Size(29, 43),
          anchor: new google.maps.Point(14, 42)
        };

        var iconGreen = {
          url: 'resources/img/icons/MarkerGreenSt-".$nagMapR_IconStyle.".png',
          size: new google.maps.Size(29, 43),
          anchor: new google.maps.Point(14, 42)
        };

        var iconOrange = {
          url: 'resources/img/icons/MarkerOrangeSt-".$nagMapR_IconStyle.".png',
          size: new google.maps.Size(29, 43),
          anchor: new google.maps.Point(14, 42)
        };

        var iconYellow = {
          url: 'resources/img/icons/MarkerYellowSt-".$nagMapR_IconStyle.".png',
          size: new google.maps.Size(29, 43),
          anchor: new google.maps.Point(14, 42)
        };

        var iconGrey = {
          url: 'resources/img/icons/MarkerGreySt-".$nagMapR_IconStyle.".png',
          size: new google.maps.Size(29, 43),
          anchor: new google.maps.Point(14, 42)
        };

        function initialize() {
          window.map = new google.maps.Map(document.getElementById('map'),{
            zoom: ".$nagMapR_MapZoom.",
            center: new google.maps.LatLng(".$nagMapR_MapCentre."),
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false,
            ");

      if($nagMapR_Style == ''){
        echo ('mapTypeId: google.maps.MapTypeId.'.$nagMapR_MapType);
      }
      else{
        echo ("styles: ");
        require "styles/$nagMapR_Style.json";
      }
      echo("\n          });");
    }
    else{
      if($nagMapR_LeafletStyle == "")
        $nagMapR_LeafletStyle = "//{s}.tile.osm.org/{z}/{x}/{y}.png";
      echo("
        var iconRed = L.icon({
          iconUrl: 'resources/img/icons/MarkerRedSt-".$nagMapR_IconStyle.".png',
          iconSize: [29, 43],
          iconAnchor: [14, 42],
          popupAnchor: [0, -42]
        });

        var iconGreen = L.icon({
          iconUrl: 'resources/img/icons/MarkerGreenSt-".$nagMapR_IconStyle.".png',
          iconSize: [29, 43],
          iconAnchor: [14, 42],
          popupAnchor: [0, -42]
        });

        var iconOrange = L.icon({
          iconUrl: 'resources/img/icons/MarkerOrangeSt-".$nagMapR_IconStyle.".png',
          iconSize: [29, 43],
          iconAnchor: [14, 42],
          popupAnchor: [0, -42]
        });

        var iconYellow = L.icon({
          iconUrl: 'resources/img/icons/MarkerYellowSt-".$nagMapR_IconStyle.".png',
          iconSize: [29, 43],
          iconAnchor: [14, 42],
          popupAnchor: [0, -42]
        });

        var iconGrey = L.icon({
          iconUrl: 'resources/img/icons/MarkerGreySt-".$nagMapR_IconStyle.".png',
          iconSize: [29, 43],
          iconAnchor: [14, 42],
          popupAnchor: [0, -42]
        });

        function initialize() {

          map = L.map('map',{zoomControl:false}).setView([".$nagMapR_MapCentre."], ".$nagMapR_MapZoom.");

          L.tileLayer('".$nagMapR_LeafletStyle."', {attribution:'&copy; <a href=\"http://osm.org/copyright\">OpenStreetMap</a> contributors.'}).addTo(map);");
    }
    ?>    

    // generating dynamic code from here
    // if the page ends here, there is something seriously wrong, please contact joao_carlos.r@hotmail.com for help

    //Filling array of Polylines
    <?php
  // print the body of the page here
    echo $linesArray;
    echo ("\n\n");
    echo $javascript;
    if($nagMapR_MapAPI == 1)
      echo("
        oms = new OverlappingMarkerSpiderfier(map);
        popup = new L.Popup();
        for(let i = 0; i < MARK.length ; i ++)
          oms.addMarker(MARK[i]);
        oms.addListener('click', function(marker) {
          openPopup(MARK.indexOf(marker), false);
        });
        ");
    echo ('};
      </script>
      </head>
      <body style="margin:0px; padding:0px; overflow:hidden;" onload="initialize()">');
    if ($nagMapR_Debug == 1){
      echo ('<a href="debugInfo/index.php"><div id="div_fixa" class="div_fixa" style="z-index:2000;"><button id="div_fixa_btn" class="button" style="vertical-align:middle"><span>Debug page</span></button></div></a><script>var debug = document.getElementById("div_fixa_btn").style;  debug.setProperty( "opacity", "1" ); debug.setProperty( "margin-top", "65px", ""); setTimeout(()=>{ debug.setProperty( "opacity", "0.6" ); debug.setProperty( "margin-top", "0px", ""); setTimeout(()=>{debug.setProperty( "opacity", "0.2" );}, 1000); }, 3500);</script>');
    }
    if ($nagMapR_ChangesBar == 1) {
      echo '<div id="map" style="width:100%; height:'.(100-$nagMapR_ChangesBarSize).'%; float: left"></div>';

      if($nagMapR_ChangesBarMode == 3){
        echo ('<iframe src="resources/changesBarLite.php?key='.$nagMapR_key.'" width="100%" height="'.$nagMapR_ChangesBarSize.'%;" style="border:none;background:black;"></iframe>');
      }
      else{
        if($nagMapR_BarFilter == 1)
          echo '<div class="form-group"><input style="font-size:'.$nagMapR_FontSize.'px;" type="text" id="searchBar" class="form-control" placeholder="'.$filter.'..."><div class="cleanS" onclick="$(\'#searchBar\').val(\'\');search();" style="font-size:'.$nagMapR_FontSize.'px;" title="'.$clear.'"><span>'.$clear.'</span></div></div>';
        echo '<div id="changesbar" style="padding-top:2px; padding-left: 1px; background: black; height:'.$nagMapR_ChangesBarSize.'%; overflow:auto;">';
        if($nagMapR_ChangesBarMode == 2){
          echo('<div id="downHosts"></div><div id="critHosts"></div><div id="warHosts"></div>');
        }
        echo('</div>');
      }
    } else {
      echo '<div id="map" style="width:100%; height:100%; float: left"></div>';
    }

    ?>

    <script type="text/javascript">

    
    function now(){ 
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

      <?php

      if($nagMapR_DateFormat == 1){
        echo ("return(date = str_day + '/' + str_month + '/' + year"); 
      } elseif ($nagMapR_DateFormat == 2) {
        echo ("return(date = str_month + '/' + str_day + '/' + year");
      }elseif ($nagMapR_DateFormat == 3) {
        echo ("return(date = year + '/' + str_month + '/' + str_day");
      }

      echo (" + ' ' + str_hours + ':' + str_minutes + ':' + str_seconds);");

      echo ('};');
      ?>

      function openPopup(host, search){
        if(search){
          for(var i = 0 ; i < hostStatus.length ; i++) 
          {
            if(hostStatus[i].nagios_host_name == hostStatusPre[host].nagios_host_name){
              host = i;
              break;
            }
          }
        }

        <?php

        if($nagMapR_MapAPI == 0)
          echo("
            clicked = true;

            for(let i = 0; i < INFO.length ; i++){
              if(i == host){
                INFO[host].open(map, MARK[host]);
              }
              else{
                INFO[i].close(map);
              }
            }
            setTimeout( function(){clicked = false;}, 500)\n");

        else{
          echo("MARK[host].bindPopup(MARK[host].options.popupContent);
            MARK[host].openPopup();
            MARK[host].unbindPopup();\n");
        }

        echo('};');
      ?> 

      function changeLines(host, color){
        if(Array.isArray(hostStatus[host].parents)){
          for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
            for (var ii = LINES.length - 1; ii >= 0; ii--) {
              if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
                <?php
              echo("LINES[ii].line.set");
              ($nagMapR_MapAPI == 0) ? print("Options({strokeColor: color});\n") : print("Style({color: color});\n");
              ?>
            }          
          }
        }
      };

      function changeIcon(host, icon, time, zindex){
        MARK[host].setIcon(icon);
        <?php
        if($nagMapR_MapAPI == 0){
          echo ("
            if(time == 0)
              time = 500;
            else
              time = 15000;
            MARK[host].setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(function () {MARK[host].setAnimation(null);}, time);
            MARK[host].setZIndex(zindex);
            \n");
        }
        else{
          echo("
            if(time == 0)
              time = 1;
            else
              time = 20;
            if(MARK[host].isBouncing())
              MARK[host].stopBouncing();
            else
              MARK[host].bounce(time);

            MARK[host].setZIndexOffset(zindex*1000);
            \n");
        }
        ?>
      };

      function createLine(index, hostA, hostB, lineColor, map){
        <?php

        if($nagMapR_MapAPI == 0){
          echo ("
            LINES[index].line = new google.maps.Polyline({
             path: [hostA, hostB],
             strokeColor: lineColor,
             strokeOpacity: 0.8,
             strokeWeight: 1.5});
             LINES[index].line.setMap(map);
             \n");
        }
        else{
          echo("
            LINES[index].line = new L.Polyline([hostA,hostB],
            {color: lineColor, 
              weight: 1.5,
              opacity: 0.8,
              smoothFactor: 1});
              LINES[index].line.addTo(map);
              \n");
        }

        ?>
      };

function updateStatus(host, status){  // Updates the status of the host informed and apply the animations

  if(status == 0){

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1){
        echo ('
          var newUp = ("<div onclick=\'openPopup("+host+",false);\' class=\"changesBarLine UP\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

          if(hostStatus[host].status == 1){
            newUp += "'. $warning .'";
          }
          else if(hostStatus[host].status == 2){
            newUp += "'. $critical .'";
          }
          else if(hostStatus[host].status == 3){
            newUp += "'. $down .'";
          }
          else{
            newUp += "'. $unknown .'";
          }

          newUp +=  " → '. $up .'</div>";
          ');
      }
    }
    ?>

    hostStatus[host].status = status;

    <?php
    if($nagMapR_Lines == 1){
      echo ("changeLines(host, '#59BB48');\n");
    }
    ?>

    changeIcon(host, iconGreen, 0, 2);

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1)
        echo ('newDivs = newUp.concat(newDivs);');
    }
    ?>

  }else if (status == 1) {

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1){
        echo (' 
          var newUp = ("<div onclick=\'openPopup("+host+",false);\' class=\"changesBarLine WAR\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

          if(hostStatus[host].status == 0){
            newUp += "'. $up .'";
          }
          else if(hostStatus[host].status == 2){
            newUp += "'. $critical .'";
          }
          else if(hostStatus[host].status == 3){
            newUp += "'. $down .'";
          }
          else{
            newUp += "'. $unknown .'";
          }

          newUp +=  " → '. $warning .'</div>";
          ');
      }
    }
    ?>

    <?php
    if($nagMapR_Lines == 1){
      echo ("changeLines(host, '#ffff00');\n");
    }
    ?>

    changeIcon(host, iconYellow, 0, 3);

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1)
        echo ('newDivs = newUp.concat(newDivs);');
    }
    ?>

    hostStatus[host].status = status;
  }else if (status == 2) {

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1){
        echo ('
          var newUp = ("<div onclick=\'openPopup("+host+",false);\' class=\"changesBarLine CRIT\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

          if(hostStatus[host].status == 0){
            newUp += "'. $up .'";
          }
          else if(hostStatus[host].status == 1){
            newUp += "'. $warning .'";
          }
          else if(hostStatus[host].status == 3){
            newUp += "'. $down .'";
          }
          else{
            newUp += "'. $unknown .'";
          }

          newUp +=  " → '. $critical .'</div>";
          ');
      }
    }
    ?>

    <?php
    if($nagMapR_Lines == 1){
      echo ("changeLines(host, '#ff6a00');\n");
    }
    ?>

    changeIcon(host, iconOrange, 0, 4);

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1)
        echo ('newDivs = newUp.concat(newDivs);');
    }
    ?>

    hostStatus[host].status = status;
  } else if (status == 3) {

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1){
        echo (' 
          var newUp = ("<div onclick=\'openPopup("+host+",false);\' class=\"changesBarLine DOWN\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

          if(hostStatus[host].status == 0){
            newUp += "'. $up .'";
          }
          else if(hostStatus[host].status == 1){
            newUp += "'. $warning .'";
          }
          else if(hostStatus[host].status == 2){
            newUp += "'. $critical .'";
          }
          else{
            newUp += "'. $unknown .'";
          }

          newUp +=  " → '. $down .'</div>";
          ');
      }
    }
    ?>

    hostStatus[host].status = status;

    <?php
    if($nagMapR_Lines == 1){
      echo ("changeLines(host, '#ff0000');\n");
    }
    ?>

    changeIcon(host, iconRed, 1, 5);

    <?php
    if($nagMapR_PlaySound ==1)
      echo ("audio.play();")
    ?>

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1)
        echo ('newDivs = newUp.concat(newDivs);');
    }
    ?>

  } else {
    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1){
        echo (' 

          var newUp = ("<div onclick=\'openPopup("+host+",false);\' class=\"changesBarLine UNK\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

          if(hostStatus[host].status == 0){
            newUp += "'. $up .'";
          }
          else if(hostStatus[host].status == 1){
            newUp += "'. $warning .'";
          }
          else if(hostStatus[host].status == 2){
            newUp += "'. $critical .'";
          }
          else if(hostStatus[host].status == 3){
            newUp += "'. $down .'";
          }
          else{
            newUp += "'. $unknown .'";
          }

          newUp +=  " → '. $unknown .'</div>";
          ');
      }
    }
    ?>

    hostStatus[host].status = status;

    <?php
    if($nagMapR_Lines == 1){
      echo ("changeLines(host, '#A9ABAE');\n");
    }
    ?>

    changeIcon(host, iconGrey, 0, 2);

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
      if($nagMapR_ChangesBarMode == 1)
        echo ('newDivs = newUp.concat(newDivs);');
    }
    ?>
  }

}
<?php
if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
  if($nagMapR_ChangesBarMode == 1){
    echo ("var newDivs = \"\";");
  }

  if($nagMapR_ChangesBarMode == 2){
    echo('

      function addHost(i, status, time){

        var insert = ("<div onclick=\"openPopup("+i+", false);\" class=\"changesBarLine " + status + " news\" id=\"" + hostStatus[i].nagios_host_name + "-" + status + "\" style=\"font-size: '. $nagMapR_FontSize .'px; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + time + "'. $timeSuffix .'</div>");

        if(status ==  "WAR"){
          document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", insert);
        }
        if(status ==  "CRIT"){
          document.getElementById(\'critHosts\').insertAdjacentHTML("afterbegin", insert);
        }
        if(status ==  "DOWN"){
          document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", insert);
        }

        var div = document.getElementById(hostStatus[i].nagios_host_name+"-"+status);
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

        var insert = ("style=\"font-size: '. $nagMapR_FontSize .'px;\">" + hostStatusPre[i].alias + " - ('. $waiting .')</div>");

        if(hostStatusPre[i].status == 1){
          document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", "<div onclick=\"openPopup("+i+", true);\" class=\"changesBarLine WAR news\" id=\"" + name + "-WAR\"" + insert);
        }
        if(hostStatusPre[i].status == 2){
          document.getElementById(\'critHosts\').insertAdjacentHTML("afterbegin", "<div onclick=\"openPopup("+i+", true);\" class=\"changesBarLine CRIT news\" id=\"" + name + "-CRIT\"" + insert);
        }
        if(hostStatusPre[i].status == 3){
          document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", "<div onclick=\"openPopup("+i+", true);\" class=\"changesBarLine DOWN news\" id=\"" + name + "-DOWN\"" + insert);
        }
      }

          //hostStatusPre = [];
      ');
  }
}
?>

realTimeUp = true;

setInterval(function(){ // Request the array with the update status of each host.

  var rq = new XMLHttpRequest();

  var arrayHosts;

  rq.open('POST', 'update.php', true);

  rq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

  rq.send('key=<?php echo $nagMapR_key ?>');

  rq.onreadystatechange = function(){

    if(rq.readyState == 4) {
      try{

        if(rq.status != 200)
          throw new Error('Failed to request update.php');

        arrayHosts = JSON.parse(rq.responseText); 

        <?php
        if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
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
          if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
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
          if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3){
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
          if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3 && $nagMapR_BarFilter == 1){
            echo ("
              if($('#searchBar').val().toLowerCase() != '')
                search();
              ");
          }
          ?>         

          if(realTimeUp == false){
            realTimeUp = true;
            toastr["success"]("<?php echo $updateErrorSolved; ?>");
          }
        }
        catch(err){

          realTimeUp = false;

          if(err.message == "Unexpected token < in JSON at position 0"){
            toastr["error"]("<?php echo $updateError; ?>");
            console.warn("<?php echo $updateErrorStatus; ?>\n" + err);
          }
          else if(err.message == "Failed to request update.php"){
            toastr["error"]("<?php echo $updateError; ?>");
            console.warn("<?php echo $updateErrorServ; ?>\n" + err);
          }
          else if(err.message == "Cannot read property 'status' of undefined"){
            toastr["error"]("<?php echo $updateError; ?>");
            console.warn("<?php echo $updateErrorChanges; ?>\n" + err);
          }
          else{            
            <?php
            if($nagMapR_Debug == 0 ){
              if($nagMapR_Reporting == 1)
                echo('
                  if(!waitToWarning)
                    toastr["error"]("'.$updateError.'");
                  ');
              else
                echo('
                  toastr["error"]("'.$updateError.'");
                  ');
            }
            ?>
            console.warn(err);
            throw err;
          }
        }
      }
    };
  }, <?php echo $nagMapR_TimeUpdate; ?>000);

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
      waitToWarning = true;
      setTimeout(function(){waitToWarning = false}, 21000);
      reportError(msg, url, lineNo, error);
    }
    ');
  else
    echo ('}');
}
else{
  if($nagMapR_Reporting == 1)
    echo ('

      window.onerror = function (msg, url, lineNo, columnNo, error) {
        waitToWarning = true;
        setTimeout(function(){waitToWarning = false}, 21000);
        reportError(msg, url, lineNo, error);
      }
      ');
}
?>
</script>
<script src="debugInfo/resources/js/jquery.min.js"></script>
<script src="resources/toastr/toastr.min.js"></script>
<script src="resources/sa/sweetalert2.all.min.js"></script>
<script src="resources/fireworks.v2.0.0-release.js"></script>

<?php
if($nagMapR_Reporting == 1 ) // Used for encryption
echo('
  <script type="text/javascript" src="resources/reporter/BigInt.js"></script>

  <script type="text/javascript" src="resources/reporter/Barrett.js"></script>

  <script type="text/javascript" src="resources/reporter/RSA_Stripped.js"></script>

  <script type="text/javascript" src="resources/reporter/js.cookie.js"></script>
  ');
  ?>
  <script type="text/javascript">

    <?php
    if($nagMapR_MapAPI == 0){
      echo('
        $(document).on("click", function(e) {
          var divNome = document.querySelector(".gm-style-iw");
          if(divNome){
            var fora = !divNome.contains(e.target);
            if (fora && !clicked){
              for(i = 0; i < INFO.length ; i++){
                INFO[i].close(map);
              } 
            }
          }
        });');
    }
    ?>

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

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3 && $nagMapR_BarFilter == 1)
      echo ("
        function filterBy(self){
          $('#searchBar').val($(self).children().next().next().text().substring(1));
          search();
        };


        $('#searchBar').keyup(function(){
          search();
        });

        function search(){
          var query = \$('#searchBar').val().toLowerCase();
          $('#changesbar .changesBarLine').each(function(){
            var \$this = \$(this);
            if(\$this.text().toLowerCase().indexOf(query) === -1)
             \$this.closest('#changesbar .changesBarLine').hide();
           else 
            \$this.closest('#changesbar .changesBarLine').show();
        });
      };
      ");
    ?>

    function reportReturn(type){
      toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "showDuration": "300",
        "timeOut": "20000",
        "extendedTimeOut": "0",
      };

      if(type < 1)
      {
        toastr["info"]("<?php echo ($reporterErrorPre) ?>");
        if(type == -1)
          toastr["error"]("<?php echo ($reporterError) ?>");
        if(type == -2)
          toastr["error"]("<?php echo ($reporterErrorOF) ?>");
      }

      if(type == 1)
        toastr["info"]("<?php echo ($errorFound) ?>", "<?php echo ($error . $reported) ?>");

      toastr.options = {
        "closeButton": false,
        "progressBar": false,
        "showDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000"
      };
    };

    if(<?php if($nagMapR_version != $nagMapR_CurrVersion) echo 'true'; else echo 'false'; ?>){
    var mouseInterval = null;
    var launchInt = null;
    var loopInt = null;
    releaseV2();
      swal({
        title: '<?php echo $newVersion; ?>!<br>v<?php echo $nagMapR_CurrVersion; ?>',
        html: '<br><center><svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="100%" height="173px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 194.9 84.4" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><style type="text/css"><![CDATA[.fil0 {fill:none}.fil1 {fill:#D2D3D5}.fil2 {fill:#007F00}.fil4 {fill:#155400}.fil3 {fill:#D2D3D5;fill-rule:nonzero}.fil5 {fill:#201E1E;fill-rule:nonzero}]]></style></defs><g id="Layer_x0020_1"><metadata id="CorelCorpID_0Corel-Layer"/><rect class="fil0" width="194.9" height="84.4"/><path id="svg_2" class="fil1" d="M111.9 80.9c-1,-4.6 -2.6,-8.5 -4.7,-12.1 -1.5,-2.7 -3.2,-5.1 -4.8,-7.7 -0.6,-0.9 -1,-1.8 -1.5,-2.7 -1.1,-1.7 -1.9,-3.8 -1.8,-6.5 0,-2.6 0.8,-4.7 1.9,-6.5 1.8,-2.8 4.8,-5.1 8.8,-5.7 3.3,-0.5 6.4,0.3 8.6,1.6 1.8,1.1 3.2,2.5 4.3,4.1 1.1,1.7 1.9,3.8 1.9,6.4 0,1.4 -0.2,2.7 -0.5,3.7 -0.3,1.1 -0.8,2 -1.3,2.9 -0.9,1.9 -2,3.6 -3.1,5.3 -3.3,5 -6.5,10.1 -7.8,17.2l0 0z"/><path id="svg_2_0" class="fil2 fillPinAnimation" d="M110.8 80.2c-1,-4.7 -2.7,-8.5 -4.7,-12.1 -1.5,-2.7 -3.2,-5.1 -4.8,-7.7 -0.6,-0.9 -1,-1.8 -1.5,-2.7 -1.1,-1.8 -1.9,-3.8 -1.9,-6.5 0.1,-2.7 0.9,-4.8 2,-6.5 1.8,-2.8 4.8,-5.1 8.8,-5.8 3.3,-0.5 6.4,0.4 8.6,1.7 1.8,1 3.2,2.4 4.3,4.1 1.1,1.7 1.8,3.8 1.9,6.4 0,1.4 -0.2,2.6 -0.5,3.7 -0.3,1.1 -0.8,1.9 -1.3,2.9 -0.9,1.8 -2,3.5 -3.1,5.2 -3.3,5.1 -6.5,10.2 -7.8,17.3l0 0z"/><path class="fil3" d="M15.3 6.6l11.5 10c1.1,1 1.9,1.4 2.3,1.4 0.8,0 1.2,-0.8 1.2,-2.5 0,-2 -0.1,-3.3 -0.3,-4 -0.2,-0.7 -0.7,-1.2 -1.5,-1.6 -0.6,-0.3 -1,-0.6 -1.2,-0.9 -0.3,-0.4 -0.4,-0.8 -0.4,-1.3 0,-1.3 0.9,-2.1 2.7,-2.5 0.7,-0.2 1.9,-0.2 3.7,-0.2 2.4,0 4,0.1 4.9,0.4 1.4,0.4 2.1,1.2 2.1,2.4 0,1 -0.6,1.8 -1.7,2.3 -0.5,0.2 -0.8,0.4 -1,0.5 -0.2,0.2 -0.3,0.4 -0.4,0.7 -0.4,1 -0.7,3 -0.7,5.8l0 7.5c0,1.2 0,2.7 0,4.7 0.1,2.8 -0.1,4.7 -0.4,5.9 -0.4,1.5 -1.2,2.2 -2.3,2.3 -0.5,-0.1 -0.9,-0.1 -1.2,-0.4 -0.4,-0.2 -1,-0.6 -1.8,-1.3l-13.6 -12.3c-1,-0.9 -1.8,-1.3 -2.4,-1.3 -1,0 -1.4,1.3 -1.4,3.9 0,2.1 0.1,3.6 0.3,4.4 0.3,0.8 0.8,1.4 1.5,1.7 1,0.5 1.5,1.2 1.5,2 0,2.2 -2.3,3.2 -6.9,3.2 -2,0 -3.7,-0.2 -4.8,-0.8 -1.1,-0.5 -1.7,-1.3 -1.7,-2.3 0,-0.5 0.1,-0.9 0.3,-1.2 0.2,-0.2 0.7,-0.6 1.5,-1.1 0.7,-0.4 1.1,-1.1 1.3,-2.2 0.2,-2 0.4,-4.9 0.4,-8.6 0,-4 -0.3,-6.9 -0.7,-8.7 -0.2,-0.6 -0.4,-1.1 -0.7,-1.5 -0.2,-0.3 -0.7,-0.6 -1.3,-0.9 -0.7,-0.3 -1.1,-0.7 -1.4,-1 -0.3,-0.3 -0.4,-0.7 -0.4,-1.2 0,-1.4 1.3,-2.4 3.8,-2.7 0.9,-0.2 2.4,-0.2 4.5,-0.2 1.3,0 2.1,0.1 2.7,0.3 0.6,0.1 1.2,0.6 2,1.3z"/><path id="1" class="fil3" d="M54.2 13.9c2.7,0 5,0.7 6.9,2.1 0.9,0.7 1.6,1.5 2,2.5 0.4,0.9 0.6,2.2 0.6,3.8l0 3.4c0,2.4 0.1,4 0.5,4.7 0.1,0.3 0.3,0.5 0.5,0.6 0.1,0.1 0.4,0.2 0.9,0.3 0.5,0.1 0.8,0.4 0.8,1 0,0.7 -0.3,1.5 -0.9,2.3 -0.6,0.8 -1.4,1.4 -2.3,2 -1.2,0.6 -2.4,1 -3.6,1 -1.7,0 -2.9,-0.6 -3.9,-1.7 -0.3,-0.4 -0.6,-0.6 -0.9,-0.6 -0.3,0 -0.7,0.2 -1.2,0.6 -1.5,1.1 -3.3,1.7 -5.2,1.7 -2.1,0 -3.7,-0.4 -4.9,-1.3 -0.8,-0.6 -1.5,-1.3 -2,-2.2 -0.5,-0.9 -0.7,-1.9 -0.7,-2.9 0,-1.6 0.7,-3 2,-4.3 2,-1.9 5,-2.9 9,-2.9 0.9,0 1.5,-0.1 1.7,-0.3 0.2,-0.1 0.3,-0.5 0.3,-1.2 0,-1.6 -0.1,-2.8 -0.5,-3.5 -0.3,-0.8 -0.9,-1.1 -1.6,-1.1 -0.5,0 -0.9,0.1 -1.3,0.4 -0.3,0.3 -0.8,0.8 -1.3,1.5 -1.4,2 -2.8,2.9 -4.3,2.9 -0.8,0 -1.4,-0.2 -1.9,-0.6 -0.4,-0.5 -0.7,-1.1 -0.7,-1.8 0,-0.7 0.3,-1.5 0.9,-2.2 0.6,-0.7 1.4,-1.4 2.4,-1.9 2.8,-1.5 5.7,-2.3 8.7,-2.3zm-1.5 13.4c-0.7,0 -1.3,0.3 -1.8,0.8 -0.5,0.6 -0.7,1.3 -0.7,2 0,0.8 0.2,1.4 0.5,1.8 0.4,0.5 0.8,0.7 1.4,0.7 1.3,0 1.9,-1 1.9,-3.1 0,-0.9 -0.1,-1.4 -0.3,-1.7 -0.2,-0.3 -0.5,-0.5 -1,-0.5z"/><path id="2" class="fil3" d="M92.4 12.7c0.4,0 0.7,0.2 1,0.7 0.3,0.4 0.5,0.9 0.5,1.5 0,0.6 -0.2,1.4 -0.6,2.2 -0.4,0.8 -0.9,1.4 -1.4,1.9 -0.4,0.3 -0.6,0.5 -0.6,0.7 0,0.1 0,0.3 0.1,0.6 0,0.3 0,0.6 0,0.8 0,1.9 -0.6,3.4 -1.8,4.7 -1.1,1.1 -2.5,1.9 -4.3,2.6 -1.7,0.6 -3.7,0.9 -5.7,0.9 -0.6,0 -1.1,0 -1.6,-0.1 -0.3,-0.1 -0.7,-0.1 -0.9,-0.1 -0.8,0 -1.2,0.4 -1.2,1.2 0,0.6 0.3,1 0.8,1.3 0.5,0.3 1.3,0.4 2.2,0.4 0.6,0 1.5,-0.1 2.7,-0.2 2.5,-0.4 4.4,-0.6 5.6,-0.6 1.8,0 3.2,0.3 4.2,0.9 0.8,0.5 1.4,1.1 1.9,2 0.5,0.8 0.7,1.7 0.7,2.7 0,1.5 -0.6,3 -1.7,4.5 -1.4,1.7 -3.4,3 -6,3.8 -2.1,0.7 -4.4,1 -7.1,1 -3.5,0 -6.2,-0.5 -8.4,-1.6 -0.9,-0.5 -1.7,-1.1 -2.3,-1.9 -0.6,-0.8 -0.9,-1.7 -0.9,-2.5 0,-0.6 0.2,-1.2 0.5,-1.7 0.3,-0.5 0.8,-0.9 1.3,-1.1 0.4,-0.2 0.7,-0.4 0.7,-0.6 0,-0.1 -0.2,-0.4 -0.6,-0.7 -1.2,-1 -1.9,-2.1 -1.9,-3.2 0,-0.8 0.3,-1.5 0.9,-2.3 0.6,-0.8 1.4,-1.4 2.2,-1.8 0.4,-0.2 0.6,-0.4 0.6,-0.6 0,-0.2 -0.2,-0.5 -0.7,-0.9 -1.6,-1.5 -2.4,-3.2 -2.4,-5.2 0,-1 0.3,-2 0.9,-2.9 0.5,-1 1.3,-1.8 2.3,-2.4 2.5,-1.6 5.2,-2.5 8.2,-2.5 1.2,0 2.6,0.2 4.1,0.5 1.4,0.3 2.3,0.4 2.8,0.4 1.7,0 3.4,-0.6 5.1,-2 0.3,-0.2 0.6,-0.4 0.8,-0.4zm-12.5 4.8c-0.8,0 -1.4,0.3 -1.9,0.9 -0.5,0.6 -0.7,1.3 -0.7,2.3 0,1.7 0.3,3 0.8,4 0.5,1 1.2,1.5 2,1.5 0.8,0 1.4,-0.3 1.9,-0.9 0.5,-0.7 0.7,-1.5 0.7,-2.5 0,-1.7 -0.2,-3 -0.7,-3.9 -0.5,-0.9 -1.2,-1.4 -2.1,-1.4zm-5.1 21c-0.3,0 -0.5,0.2 -0.5,0.7 0,0.9 0.5,1.6 1.4,2.2 1,0.5 2.3,0.8 3.9,0.8 1.5,0 2.7,-0.2 3.5,-0.6 0.9,-0.4 1.3,-0.9 1.3,-1.7 0,-0.3 -0.2,-0.7 -0.5,-0.9 -0.3,-0.3 -0.7,-0.4 -1.2,-0.4 -0.2,0 -0.6,0 -1.1,0.1 -0.8,0.1 -1.8,0.2 -2.8,0.2 -0.8,0 -1.6,-0.1 -2.3,-0.2 -0.5,0 -1,-0.1 -1.3,-0.1 -0.2,-0.1 -0.3,-0.1 -0.4,-0.1z"/><path id="3" class="fil3" d="M118.1 27.4l-2.8 6.2c-0.4,1.1 -0.8,1.8 -1.1,2.1 -0.3,0.4 -0.6,0.6 -1,0.6 -0.3,0 -0.6,-0.2 -0.9,-0.5 -0.2,-0.2 -0.5,-0.8 -1.2,-2 -0.6,-1.2 -1.1,-2.2 -1.5,-3.1l-3 -6.9c-0.4,-0.9 -0.9,-1.4 -1.3,-1.4 -0.5,0 -0.8,0.4 -0.8,1.2 -0.1,1.8 -0.2,3.5 -0.2,5.1 0,1.6 0.1,2.6 0.2,3.2 0.1,0.3 0.3,0.6 0.5,0.7 0.1,0.2 0.6,0.5 1.4,0.9 0.5,0.2 0.8,0.7 0.8,1.3 0,1.8 -2.2,2.6 -6.7,2.6 -3.7,0 -5.5,-0.8 -5.5,-2.5 0,-0.4 0,-0.7 0.2,-0.9 0.2,-0.2 0.7,-0.5 1.5,-0.9 0.5,-0.2 0.9,-0.5 1.1,-0.9 0.1,-0.4 0.3,-1.2 0.4,-2.4 0.1,-1.2 0.3,-3.2 0.4,-6 0.1,-2.8 0.2,-5.2 0.2,-7.2 0,-2.6 -0.2,-4.3 -0.6,-5.1 -0.1,-0.4 -0.3,-0.6 -0.5,-0.7 -0.1,-0.2 -0.5,-0.4 -1,-0.6 -1.1,-0.6 -1.6,-1.2 -1.6,-2.1 0,-0.5 0.2,-1 0.6,-1.5 0.4,-0.4 1,-0.7 1.6,-0.9 0.7,-0.2 1.6,-0.4 2.7,-0.5 1.2,-0.2 2.1,-0.2 2.9,-0.2 2,0 3.4,0.1 4.2,0.5 0.4,0.1 0.7,0.3 0.9,0.6 0.3,0.2 0.6,0.8 1,1.6l4.8 8.8c0.6,1 0.9,1.6 1.1,1.8 0.2,0.2 0.5,0.3 0.9,0.3 0.3,0 0.5,-0.1 0.8,-0.3 0.2,-0.2 0.5,-0.7 0.8,-1.3l3.3 -6.4c0.4,-0.8 0.6,-1.3 0.8,-1.5 0.7,-1.6 1.5,-2.6 2.2,-3.1 0.9,-0.7 2.6,-1 5.1,-1 4.4,0 6.6,1 6.6,3 0,0.4 -0.1,0.8 -0.3,1 -0.2,0.3 -0.5,0.6 -1,1 -0.7,0.6 -1.2,1.3 -1.4,2.4 -0.3,2.1 -0.5,4.7 -0.5,7.7 0,3.9 0.2,7.4 0.5,10.3 0.1,0.8 0.3,1.2 0.5,1.5 0.2,0.3 0.7,0.7 1.5,1.2 0.5,0.4 0.8,0.8 0.8,1.5 0,1 -0.7,1.8 -2.2,2.2 -0.7,0.3 -1.5,0.5 -2.3,0.5 -0.8,0.1 -2.1,0.1 -3.7,0.2 -2.6,-0.1 -4.6,-0.2 -5.9,-0.6 -1.4,-0.4 -2.1,-1.1 -2.1,-2.1 0,-0.4 0.1,-0.7 0.3,-0.9 0.2,-0.3 0.6,-0.7 1.4,-1.3 0.4,-0.4 0.7,-1 0.8,-1.9 0.2,-0.9 0.3,-2.5 0.3,-4.7 0,-1.3 -0.1,-2.1 -0.2,-2.4 -0.1,-0.4 -0.4,-0.6 -0.7,-0.6 -0.5,0 -0.9,0.3 -1.3,0.9 -0.4,0.6 -1,1.7 -1.8,3.5z"/><path id="4" class="fil3" d="M150.2 13.9c2.7,0 4.9,0.7 6.8,2.1 1,0.7 1.7,1.5 2.1,2.5 0.3,0.9 0.5,2.2 0.5,3.8l0 3.4c0,2.4 0.2,4 0.5,4.7 0.2,0.3 0.3,0.5 0.5,0.6 0.1,0.1 0.5,0.2 1,0.3 0.5,0.1 0.7,0.4 0.7,1 0,0.7 -0.3,1.5 -0.9,2.3 -0.6,0.8 -1.3,1.4 -2.3,2 -1.1,0.6 -2.3,1 -3.6,1 -1.6,0 -2.9,-0.6 -3.8,-1.7 -0.4,-0.4 -0.7,-0.6 -0.9,-0.6 -0.4,0 -0.8,0.2 -1.3,0.6 -1.5,1.1 -3.3,1.7 -5.2,1.7 -2,0 -3.7,-0.4 -4.9,-1.3 -0.8,-0.6 -1.5,-1.3 -1.9,-2.2 -0.5,-0.9 -0.8,-1.9 -0.8,-2.9 0,-1.6 0.7,-3 2,-4.3 2,-1.9 5,-2.9 9,-2.9 0.9,0 1.5,-0.1 1.7,-0.3 0.3,-0.1 0.4,-0.5 0.4,-1.2 0,-1.6 -0.2,-2.8 -0.5,-3.5 -0.4,-0.8 -0.9,-1.1 -1.7,-1.1 -0.5,0 -0.9,0.1 -1.2,0.4 -0.4,0.3 -0.9,0.8 -1.4,1.5 -1.4,2 -2.8,2.9 -4.3,2.9 -0.7,0 -1.4,-0.2 -1.8,-0.6 -0.5,-0.5 -0.7,-1.1 -0.7,-1.8 0,-0.7 0.2,-1.5 0.8,-2.2 0.6,-0.7 1.4,-1.4 2.5,-1.9 2.7,-1.5 5.6,-2.3 8.7,-2.3zm-1.6 13.4c-0.6,0 -1.2,0.3 -1.7,0.8 -0.5,0.6 -0.8,1.3 -0.8,2 0,0.8 0.2,1.4 0.5,1.8 0.4,0.5 0.9,0.7 1.5,0.7 1.2,0 1.8,-1 1.8,-3.1 0,-0.9 -0.1,-1.4 -0.3,-1.7 -0.1,-0.3 -0.5,-0.5 -1,-0.5z"/><path id="5" class="fil3" d="M175.1 14.1c1,0 1.5,0.1 1.8,0.2 0.2,0.2 0.3,0.6 0.3,1.2 0,0.6 0.2,0.9 0.6,0.9 0.1,0 0.6,-0.3 1.5,-0.9 0.7,-0.4 1.5,-0.8 2.5,-1.1 1.1,-0.3 2,-0.5 2.8,-0.5 2,0 3.7,0.7 5.3,2.1 1.1,1 2,2.3 2.7,3.9 0.7,1.6 1,3.4 1,5.2 0,3.9 -1.3,7.1 -3.9,9.6 -2,1.9 -4.3,2.9 -6.9,2.9 -1.3,0 -2.7,-0.3 -4,-0.9 -0.3,-0.2 -0.5,-0.2 -0.7,-0.2 -0.5,0 -0.7,0.4 -0.7,1.4 0,1.2 0.4,2.1 1.2,2.6 0.8,0.5 1.2,0.8 1.4,1 0.1,0.2 0.2,0.5 0.2,0.9 0,0.9 -0.5,1.7 -1.6,2.1 -1,0.6 -2.4,0.8 -4.3,0.8 -3.4,0 -6.3,-0.7 -8.8,-2 -1.3,-0.7 -1.9,-1.5 -1.9,-2.5 0,-0.5 0.1,-0.8 0.3,-1.1 0.2,-0.2 0.5,-0.5 1.1,-0.7 0.8,-0.4 1.3,-1.1 1.5,-2 0.1,-1 0.3,-3.8 0.4,-8.4 -0.1,-3.8 -0.3,-5.9 -0.4,-6.4 -0.1,-0.1 -0.2,-0.2 -0.3,-0.3 -0.1,0 -0.5,-0.1 -1.2,-0.2 -0.4,-0.1 -0.8,-0.3 -1.2,-0.7 -0.3,-0.4 -0.4,-0.8 -0.4,-1.3 0,-0.6 0.2,-1.2 0.6,-1.6 0.5,-0.5 1.2,-1 2.1,-1.4 3.8,-1.7 6.8,-2.6 9,-2.6zm2.3 8.7l-0.3 6.4c-0.1,0.2 -0.1,0.5 -0.1,0.7 0,0.5 0.2,0.9 0.6,1.2 0.5,0.3 1,0.4 1.7,0.4 2.1,0 3.2,-1.7 3.2,-5.2 0,-2.3 -0.4,-3.9 -1.3,-4.8 -0.6,-0.6 -1.3,-0.9 -2,-0.9 -1.1,0 -1.7,0.8 -1.8,2.2z"/><path class="fil3" d="M16.4 64.3c-0.5,0 -0.8,0.1 -0.9,0.4 -0.1,0.3 -0.2,1 -0.2,2 0,1.6 0.1,2.9 0.3,3.8 0.1,0.3 0.2,0.5 0.3,0.6 0.1,0.1 0.4,0.3 0.7,0.5 0.8,0.4 1.2,1 1.2,1.7 0,0.8 -0.6,1.4 -1.6,1.7 -1.1,0.4 -2.9,0.6 -5.4,0.6 -4.7,0 -7,-0.9 -7,-2.7 0,-0.3 0,-0.5 0.2,-0.7 0.1,-0.2 0.4,-0.5 0.9,-0.8 0.6,-0.4 0.9,-0.8 1.1,-1.3 0.2,-0.5 0.4,-1.4 0.5,-2.6 0,-0.9 0.1,-4.1 0.1,-9.7 0,-1.8 -0.1,-3 -0.3,-3.6 -0.2,-0.6 -0.6,-1.1 -1.2,-1.4 -0.5,-0.2 -0.9,-0.5 -1.1,-0.7 -0.2,-0.2 -0.3,-0.5 -0.3,-0.9 0,-0.4 0.2,-0.9 0.6,-1.2 0.3,-0.4 0.8,-0.6 1.5,-0.8 0.5,-0.1 1.3,-0.2 2.2,-0.2 0.5,0 1.5,0.1 3.2,0.2 0.3,0 0.8,0 1.5,0 1.1,0 2.6,0 4.5,-0.2 1.3,-0.1 2.1,-0.1 2.6,-0.1 2.7,0 4.9,0.6 6.7,1.9 1.6,1.2 2.4,2.9 2.4,5 0,1.1 -0.3,2.2 -0.8,3.1 -0.6,1 -1.4,1.7 -2.3,2.2 -0.4,0.2 -0.6,0.4 -0.6,0.7 0,0.4 0.3,0.7 0.9,0.9 1.3,0.5 2.3,1.2 2.9,2.2 0.6,1 1.1,2.5 1.4,4.5 0.1,0.8 0.2,1.4 0.5,1.6 0.2,0.3 0.7,0.5 1.5,0.7 0.2,0.1 0.4,0.3 0.6,0.5 0.1,0.3 0.2,0.5 0.2,0.9 0,0.3 -0.1,0.7 -0.4,1.1 -0.3,0.4 -0.7,0.8 -1.2,1 -1.1,0.5 -2.5,0.7 -4.4,0.7 -2.5,0 -4.2,-0.6 -5.3,-1.9 -0.5,-0.6 -0.9,-1.4 -1.2,-2.2 -0.4,-0.9 -0.8,-2.3 -1.2,-4.2 -0.3,-1.2 -0.7,-2 -1.1,-2.5 -0.5,-0.5 -1.1,-0.8 -2,-0.8zm-1.1 -9.7l-0.2 4.4c0,0 0,0.1 0,0.2 0,0.4 0.1,0.7 0.2,0.9 0.1,0.1 0.4,0.2 0.8,0.2 1.1,0 1.9,-0.3 2.5,-0.9 0.5,-0.5 0.7,-1.5 0.7,-2.7 0,-2.5 -0.8,-3.7 -2.6,-3.7 -0.5,0 -0.8,0.1 -1,0.3 -0.2,0.3 -0.4,0.7 -0.4,1.3z"/><path id="1" class="fil3" d="M41.6 49.2l9.8 0c2,0 3.4,0 4.3,-0.1 0.9,-0.1 1.8,-0.3 2.7,-0.5 0.2,-0.1 0.4,-0.1 0.6,-0.1 0.6,0 1.2,0.3 1.7,1 0.9,1.2 1.3,2.6 1.3,4.2 0,0.8 -0.2,1.5 -0.5,2 -0.4,0.4 -0.9,0.7 -1.5,0.7 -0.4,0 -0.7,-0.1 -0.9,-0.3 -0.3,-0.1 -0.6,-0.5 -1,-1.1 -0.5,-0.9 -1.3,-1.4 -2.1,-1.7 -0.9,-0.3 -2.3,-0.5 -4.2,-0.5 -1.5,0 -2.4,0.3 -2.7,0.8 -0.2,0.3 -0.3,0.7 -0.3,1.1 0,0.5 0,1.7 0,3.6 0,0.8 0.1,1.3 0.3,1.5 0.3,0.1 1,0.2 2,0.2 0.6,0 1,-0.1 1.2,-0.3 0.2,-0.2 0.4,-0.6 0.5,-1.2 0.1,-0.7 0.1,-1.2 0.2,-1.5 0.1,-0.3 0.3,-0.5 0.6,-0.7 0.2,-0.2 0.5,-0.4 0.9,-0.4 0.5,0 1,0.3 1.3,0.8 0.3,0.4 0.5,1 0.7,1.9 0.2,1 0.3,1.9 0.3,3 0,2 -0.3,3.6 -1,4.9 -0.3,0.7 -0.8,1 -1.5,1 -0.4,0 -0.7,-0.1 -0.9,-0.3 -0.2,-0.2 -0.3,-0.7 -0.4,-1.4 -0.2,-1.4 -1,-2.1 -2.5,-2.1 -0.6,0 -1,0.1 -1.2,0.5 -0.2,0.4 -0.3,1 -0.3,2 0,0.6 0.1,1.4 0.1,2.3 0.1,1 0.2,1.6 0.3,1.9 0.1,0.4 0.4,0.7 0.9,0.8 0.6,0.1 1.4,0.2 2.7,0.2 1.9,0 3.3,-0.2 4.3,-0.7 0.9,-0.5 1.7,-1.4 2.3,-2.7 0.4,-0.7 0.7,-1.2 1,-1.5 0.3,-0.2 0.7,-0.3 1.1,-0.3 0.5,0 1,0.2 1.3,0.7 0.4,0.4 0.5,1 0.5,1.7 0,0.8 -0.1,1.7 -0.5,2.9 -0.4,1.1 -0.9,2.1 -1.5,3 -0.3,0.4 -0.5,0.7 -0.7,0.9 -0.2,0.1 -0.4,0.2 -0.7,0.2 -0.3,0 -0.6,-0.1 -1.1,-0.1 -0.8,-0.2 -1.9,-0.3 -3.2,-0.3l-16.2 0c-0.6,0 -1.1,-0.2 -1.5,-0.4 -0.3,-0.3 -0.5,-0.7 -0.5,-1.3 0,-0.3 0.1,-0.6 0.2,-0.8 0.2,-0.1 0.5,-0.3 1.1,-0.6 0.3,-0.1 0.6,-0.3 0.8,-0.6 0.2,-0.2 0.3,-0.6 0.4,-1.1 0.5,-1.9 0.7,-4.5 0.7,-7.7 0,-4.9 -0.3,-8 -0.9,-9.3 -0.2,-0.2 -0.3,-0.4 -0.5,-0.5 -0.1,-0.1 -0.4,-0.2 -0.9,-0.4 -0.7,-0.2 -1.1,-0.7 -1.1,-1.4 0,-0.4 0.1,-0.8 0.4,-1.1 0.2,-0.3 0.5,-0.5 0.9,-0.6 0.5,-0.1 1.5,-0.2 2.9,-0.2z"/><path id="2" class="fil3" d="M78.3 75.2l-2.5 0c-1.8,0 -3.1,0.1 -4,0.1 -0.7,0.1 -1.2,0.1 -1.5,0.1 -0.8,0 -1.4,-0.2 -1.9,-0.6 -0.4,-0.4 -0.7,-0.9 -0.7,-1.4 0,-0.4 0.1,-0.6 0.3,-0.8 0.2,-0.2 0.6,-0.6 1.2,-1 0.4,-0.2 0.6,-0.6 0.8,-1.2 0.1,-0.5 0.2,-1.7 0.4,-3.5 0.3,-2.7 0.4,-6.1 0.4,-10.2 0,-1.5 -0.1,-2.4 -0.3,-2.9 -0.2,-0.5 -0.7,-0.9 -1.4,-1.2 -0.8,-0.4 -1.3,-0.9 -1.3,-1.6 0,-0.6 0.3,-1 0.8,-1.4 0.5,-0.3 1.1,-0.5 1.9,-0.5 0.5,0 1,0 1.7,0 1.1,0.1 2.3,0.1 3.6,0.1 1,0 2.8,0 5.6,-0.1 0.9,0 1.7,0 2.4,0 1.7,0 3,0.1 4.1,0.4 1,0.3 1.9,0.7 2.7,1.4 1.2,1 1.8,2.4 1.8,4.2 0,0.9 -0.1,1.7 -0.5,2.5 -0.4,0.9 -0.9,1.5 -1.5,2 -0.4,0.4 -0.7,0.7 -0.7,1 0,0.3 0.3,0.6 0.8,0.8 1.1,0.4 2,1.1 2.9,2.1 0.8,1 1.3,2.3 1.3,3.9 0,1.8 -0.7,3.5 -2,5 -1.8,2 -4.4,3 -7.8,3 -0.5,0 -1.7,-0.1 -3.5,-0.1 -1.7,-0.1 -2.7,-0.1 -3.1,-0.1zm1.3 -20.7l0 3.9c0,0.7 0.1,1.1 0.3,1.4 0.1,0.2 0.4,0.3 0.8,0.3 0.9,0 1.6,-0.3 2.1,-0.8 0.4,-0.6 0.6,-1.5 0.6,-2.7 0,-1.2 -0.2,-2.2 -0.7,-2.8 -0.4,-0.7 -1.1,-1 -1.9,-1 -0.4,0 -0.7,0.1 -0.9,0.4 -0.2,0.2 -0.3,0.7 -0.3,1.3zm0 11l0 1.8c0,1.6 0.1,2.8 0.4,3.3 0.2,0.5 0.7,0.8 1.5,0.8 1.9,0 2.8,-1.1 2.8,-3.4 0,-2.8 -1,-4.2 -3.2,-4.2 -0.5,0 -0.9,0.1 -1.2,0.4 -0.2,0.2 -0.3,0.6 -0.3,1.3z"/><path id="3" class="fil3" d="M141 64.3c-0.4,0 -0.7,0.1 -0.9,0.4 -0.1,0.3 -0.2,1 -0.2,2 0,1.6 0.1,2.9 0.3,3.8 0.1,0.3 0.2,0.5 0.3,0.6 0.1,0.1 0.4,0.3 0.7,0.5 0.8,0.4 1.2,1 1.2,1.7 0,0.8 -0.6,1.4 -1.6,1.7 -1.1,0.4 -2.9,0.6 -5.4,0.6 -4.7,0 -7,-0.9 -7,-2.7 0,-0.3 0,-0.5 0.2,-0.7 0.1,-0.2 0.4,-0.5 0.9,-0.8 0.6,-0.4 1,-0.8 1.2,-1.3 0.2,-0.5 0.3,-1.4 0.4,-2.6 0,-0.9 0.1,-4.1 0.1,-9.7 0,-1.8 -0.1,-3 -0.3,-3.6 -0.2,-0.6 -0.6,-1.1 -1.1,-1.4 -0.6,-0.2 -1,-0.5 -1.2,-0.7 -0.2,-0.2 -0.2,-0.5 -0.2,-0.9 0,-0.4 0.1,-0.9 0.5,-1.2 0.4,-0.4 0.9,-0.6 1.5,-0.8 0.6,-0.1 1.3,-0.2 2.3,-0.2 0.4,0 1.4,0.1 3.1,0.2 0.3,0 0.8,0 1.5,0 1.1,0 2.7,0 4.6,-0.2 1.2,-0.1 2,-0.1 2.5,-0.1 2.7,0 5,0.6 6.7,1.9 1.6,1.2 2.4,2.9 2.4,5 0,1.1 -0.2,2.2 -0.8,3.1 -0.6,1 -1.3,1.7 -2.3,2.2 -0.4,0.2 -0.6,0.4 -0.6,0.7 0,0.4 0.3,0.7 0.9,0.9 1.3,0.5 2.3,1.2 2.9,2.2 0.7,1 1.1,2.5 1.4,4.5 0.1,0.8 0.3,1.4 0.5,1.6 0.2,0.3 0.7,0.5 1.5,0.7 0.2,0.1 0.4,0.3 0.6,0.5 0.2,0.3 0.2,0.5 0.2,0.9 0,0.3 -0.1,0.7 -0.4,1.1 -0.3,0.4 -0.7,0.8 -1.2,1 -1.1,0.5 -2.5,0.7 -4.4,0.7 -2.5,0 -4.2,-0.6 -5.3,-1.9 -0.5,-0.6 -0.9,-1.4 -1.2,-2.2 -0.4,-0.9 -0.8,-2.3 -1.2,-4.2 -0.3,-1.2 -0.7,-2 -1.1,-2.5 -0.5,-0.5 -1.1,-0.8 -2,-0.8zm-1.1 -9.7l-0.2 4.4c0,0 0,0.1 0,0.2 0,0.4 0.1,0.7 0.2,0.9 0.2,0.1 0.4,0.2 0.8,0.2 1.2,0 2,-0.3 2.5,-0.9 0.5,-0.5 0.7,-1.5 0.7,-2.7 0,-2.5 -0.8,-3.7 -2.6,-3.7 -0.5,0 -0.8,0.1 -1,0.3 -0.2,0.3 -0.3,0.7 -0.4,1.3z"/><path id="4" class="fil3" d="M172.7 50.1l9.5 8.3c0.9,0.8 1.5,1.2 1.9,1.2 0.7,0 1,-0.7 1,-2.1 0,-1.6 -0.1,-2.7 -0.3,-3.3 -0.1,-0.6 -0.5,-1 -1.2,-1.3 -0.5,-0.3 -0.8,-0.5 -1,-0.8 -0.2,-0.3 -0.3,-0.6 -0.3,-1 0,-1.1 0.7,-1.8 2.2,-2.1 0.6,-0.1 1.6,-0.2 3.1,-0.2 1.9,0 3.2,0.1 4,0.3 1.1,0.4 1.7,1.1 1.7,2.1 0,0.8 -0.4,1.4 -1.4,1.8 -0.4,0.2 -0.6,0.3 -0.8,0.5 -0.1,0.1 -0.2,0.3 -0.3,0.5 -0.4,0.9 -0.6,2.5 -0.6,4.8l0 6.2c0,1 0,2.3 0,3.9 0.1,2.2 -0.1,3.8 -0.4,4.8 -0.3,1.3 -0.9,1.9 -1.8,1.9 -0.4,0 -0.8,-0.1 -1,-0.3 -0.3,-0.1 -0.8,-0.5 -1.5,-1.1l-11.2 -10.1c-0.8,-0.7 -1.5,-1.1 -2,-1.1 -0.8,0 -1.2,1.1 -1.2,3.2 0,1.8 0.1,3 0.3,3.7 0.2,0.6 0.6,1.1 1.2,1.4 0.9,0.4 1.3,0.9 1.3,1.6 0,1.8 -1.9,2.7 -5.7,2.7 -1.7,0 -3,-0.3 -4,-0.7 -0.9,-0.5 -1.4,-1.1 -1.4,-1.9 0,-0.5 0.1,-0.8 0.3,-1 0.2,-0.2 0.6,-0.5 1.2,-0.9 0.6,-0.3 0.9,-0.9 1.1,-1.9 0.2,-1.6 0.3,-3.9 0.3,-7 0,-3.3 -0.2,-5.7 -0.6,-7.2 -0.1,-0.5 -0.3,-0.9 -0.5,-1.2 -0.2,-0.3 -0.6,-0.5 -1.1,-0.7 -0.5,-0.3 -0.9,-0.6 -1.2,-0.9 -0.2,-0.2 -0.3,-0.6 -0.3,-0.9 0,-1.3 1,-2 3.1,-2.3 0.8,-0.1 2,-0.2 3.8,-0.2 1,0 1.7,0.1 2.2,0.2 0.5,0.2 1,0.6 1.6,1.1z"/><circle id="svg_4" class="fil4 fillCircleAnimation" cx="110.7" cy="51.1" r="4.4"/><path class="fil5" d="M14.3 5.1l11.5 10.1c1.1,0.9 1.9,1.4 2.4,1.4 0.8,0 1.2,-0.9 1.2,-2.6 0,-1.9 -0.1,-3.3 -0.4,-4 -0.2,-0.7 -0.7,-1.2 -1.4,-1.6 -0.6,-0.2 -1.1,-0.6 -1.3,-0.9 -0.2,-0.3 -0.4,-0.7 -0.4,-1.3 0,-1.3 0.9,-2.1 2.7,-2.5 0.7,-0.1 2,-0.2 3.8,-0.2 2.3,0 3.9,0.1 4.8,0.4 1.4,0.5 2.1,1.3 2.1,2.5 0,1 -0.6,1.7 -1.7,2.2 -0.5,0.2 -0.8,0.4 -0.9,0.6 -0.2,0.1 -0.4,0.4 -0.5,0.7 -0.4,1 -0.6,2.9 -0.6,5.7l0 7.6c0,1.1 0,2.7 0,4.7 0,2.7 -0.2,4.7 -0.5,5.9 -0.4,1.4 -1.2,2.2 -2.3,2.2 -0.4,0 -0.9,-0.1 -1.2,-0.3 -0.3,-0.2 -0.9,-0.7 -1.7,-1.4l-13.7 -12.3c-1,-0.8 -1.8,-1.2 -2.4,-1.2 -0.9,0 -1.4,1.3 -1.4,3.9 0,2.1 0.1,3.6 0.4,4.3 0.2,0.8 0.7,1.4 1.4,1.8 1.1,0.5 1.6,1.1 1.6,2 0,2.1 -2.3,3.2 -6.9,3.2 -2.1,0 -3.7,-0.3 -4.9,-0.8 -1.1,-0.6 -1.7,-1.4 -1.7,-2.4 0,-0.5 0.1,-0.9 0.3,-1.1 0.3,-0.3 0.7,-0.7 1.5,-1.1 0.7,-0.4 1.2,-1.2 1.3,-2.3 0.3,-2 0.4,-4.8 0.4,-8.6 0,-3.9 -0.2,-6.8 -0.7,-8.6 -0.2,-0.7 -0.4,-1.2 -0.6,-1.5 -0.3,-0.3 -0.7,-0.6 -1.3,-0.9 -0.7,-0.4 -1.2,-0.7 -1.5,-1 -0.2,-0.4 -0.4,-0.7 -0.4,-1.2 0,-1.5 1.3,-2.4 3.8,-2.8 0.9,-0.1 2.4,-0.2 4.6,-0.2 1.2,0 2.1,0.1 2.6,0.3 0.6,0.2 1.3,0.6 2,1.3z"/><path id="1" class="fil5" d="M53.3 12.5c2.7,0 4.9,0.7 6.8,2 1,0.7 1.7,1.6 2.1,2.5 0.4,0.9 0.6,2.2 0.6,3.9l-0.1 3.4c0,2.4 0.2,3.9 0.5,4.6 0.2,0.4 0.3,0.6 0.5,0.7 0.2,0.1 0.5,0.1 1,0.2 0.5,0.1 0.7,0.4 0.7,1 0,0.7 -0.3,1.5 -0.9,2.3 -0.6,0.8 -1.3,1.5 -2.3,2 -1.1,0.7 -2.3,1 -3.6,1 -1.6,0 -2.9,-0.5 -3.8,-1.6 -0.4,-0.4 -0.7,-0.6 -0.9,-0.6 -0.3,0 -0.7,0.2 -1.3,0.5 -1.5,1.2 -3.2,1.7 -5.2,1.7 -2,0 -3.6,-0.4 -4.9,-1.3 -0.8,-0.5 -1.5,-1.3 -1.9,-2.2 -0.5,-0.9 -0.7,-1.9 -0.7,-2.9 0,-1.6 0.6,-3 1.9,-4.2 2,-2 5,-2.9 9,-2.9 0.9,0 1.5,-0.1 1.7,-0.3 0.3,-0.2 0.4,-0.6 0.4,-1.2 0,-1.7 -0.2,-2.9 -0.5,-3.6 -0.4,-0.7 -0.9,-1.1 -1.7,-1.1 -0.5,0 -0.9,0.2 -1.2,0.4 -0.4,0.3 -0.8,0.8 -1.4,1.6 -1.4,1.9 -2.8,2.9 -4.3,2.9 -0.7,0 -1.3,-0.2 -1.8,-0.7 -0.5,-0.4 -0.7,-1 -0.7,-1.7 0,-0.8 0.3,-1.5 0.9,-2.3 0.5,-0.7 1.3,-1.3 2.4,-1.9 2.7,-1.5 5.7,-2.2 8.7,-2.2zm-1.6 13.4c-0.6,0 -1.2,0.2 -1.7,0.8 -0.5,0.5 -0.8,1.2 -0.8,2 0,0.7 0.2,1.3 0.6,1.8 0.3,0.4 0.8,0.6 1.4,0.6 1.2,0 1.8,-1 1.8,-3.1 0,-0.8 -0.1,-1.4 -0.3,-1.7 -0.1,-0.3 -0.5,-0.4 -1,-0.4z"/><path id="2" class="fil5" d="M91.5 11.3c0.3,0 0.7,0.2 1,0.6 0.3,0.5 0.4,1 0.4,1.6 0,0.6 -0.2,1.3 -0.6,2.1 -0.4,0.8 -0.8,1.4 -1.4,1.9 -0.4,0.3 -0.5,0.6 -0.5,0.8 0,0 0,0.2 0,0.5 0.1,0.3 0.1,0.6 0.1,0.9 0,1.8 -0.6,3.4 -1.9,4.6 -1.1,1.1 -2.5,2 -4.2,2.6 -1.8,0.6 -3.7,0.9 -5.8,0.9 -0.5,0 -1,0 -1.5,-0.1 -0.4,0 -0.7,0 -0.9,0 -0.8,0 -1.3,0.4 -1.3,1.2 0,0.5 0.3,0.9 0.8,1.2 0.6,0.3 1.3,0.5 2.2,0.5 0.6,0 1.5,-0.1 2.7,-0.3 2.5,-0.3 4.4,-0.5 5.6,-0.5 1.8,0 3.2,0.3 4.2,0.9 0.8,0.4 1.5,1.1 1.9,1.9 0.5,0.9 0.7,1.8 0.7,2.7 0,1.6 -0.5,3.1 -1.7,4.5 -1.3,1.8 -3.3,3 -5.9,3.9 -2.1,0.6 -4.5,0.9 -7.2,0.9 -3.4,0 -6.2,-0.5 -8.3,-1.5 -1,-0.5 -1.8,-1.2 -2.4,-2 -0.6,-0.8 -0.8,-1.6 -0.8,-2.5 0,-0.6 0.1,-1.1 0.4,-1.6 0.4,-0.5 0.8,-0.9 1.3,-1.1 0.5,-0.2 0.7,-0.4 0.7,-0.6 0,-0.2 -0.2,-0.4 -0.6,-0.8 -1.2,-1 -1.8,-2.1 -1.8,-3.2 0,-0.7 0.3,-1.5 0.9,-2.3 0.6,-0.7 1.3,-1.3 2.2,-1.8 0.3,-0.2 0.5,-0.4 0.5,-0.5 0,-0.2 -0.2,-0.6 -0.7,-1 -1.6,-1.5 -2.3,-3.2 -2.3,-5.1 0,-1.1 0.2,-2 0.8,-3 0.6,-0.9 1.3,-1.7 2.3,-2.4 2.5,-1.6 5.3,-2.4 8.3,-2.4 1.2,0 2.5,0.1 4,0.5 1.4,0.2 2.4,0.4 2.9,0.4 1.7,0 3.3,-0.7 5,-2 0.4,-0.3 0.6,-0.4 0.9,-0.4zm-12.6 4.7c-0.8,0 -1.4,0.3 -1.8,0.9 -0.5,0.6 -0.7,1.4 -0.7,2.4 0,1.6 0.2,2.9 0.7,3.9 0.5,1 1.2,1.6 2,1.6 0.8,0 1.5,-0.4 2,-1 0.4,-0.6 0.7,-1.4 0.7,-2.5 0,-1.6 -0.3,-2.9 -0.8,-3.8 -0.5,-1 -1.2,-1.4 -2.1,-1.5zm-5 21.1c-0.4,0 -0.6,0.2 -0.6,0.6 0,0.9 0.5,1.7 1.5,2.2 1,0.6 2.2,0.9 3.8,0.9 1.5,0 2.7,-0.2 3.6,-0.6 0.8,-0.4 1.2,-1 1.2,-1.7 0,-0.4 -0.1,-0.7 -0.5,-1 -0.3,-0.3 -0.7,-0.4 -1.1,-0.4 -0.3,0 -0.6,0 -1.1,0.1 -0.9,0.1 -1.8,0.2 -2.9,0.2 -0.8,0 -1.5,0 -2.3,-0.1 -0.5,-0.1 -0.9,-0.1 -1.3,-0.2 -0.1,0 -0.2,0 -0.3,0z"/><path id="3" class="fil5" d="M117.2 26l-2.8 6.2c-0.5,1 -0.9,1.7 -1.2,2.1 -0.3,0.3 -0.6,0.5 -0.9,0.5 -0.4,0 -0.7,-0.1 -0.9,-0.4 -0.2,-0.2 -0.6,-0.9 -1.2,-2.1 -0.6,-1.1 -1.1,-2.2 -1.5,-3.1l-3.1 -6.8c-0.4,-1 -0.8,-1.5 -1.3,-1.5 -0.5,0 -0.8,0.4 -0.8,1.3 -0.1,1.8 -0.2,3.5 -0.2,5 0,1.6 0.1,2.7 0.3,3.3 0.1,0.3 0.2,0.5 0.4,0.7 0.2,0.1 0.7,0.4 1.4,0.8 0.6,0.3 0.8,0.7 0.8,1.4 0,1.7 -2.2,2.6 -6.6,2.6 -3.7,0 -5.6,-0.9 -5.6,-2.5 0,-0.5 0.1,-0.8 0.3,-1 0.2,-0.2 0.7,-0.5 1.5,-0.8 0.5,-0.3 0.8,-0.6 1,-1 0.2,-0.4 0.3,-1.2 0.4,-2.3 0.2,-1.3 0.3,-3.3 0.4,-6.1 0.2,-2.8 0.2,-5.2 0.2,-7.2 0,-2.5 -0.2,-4.2 -0.6,-5.1 -0.1,-0.3 -0.2,-0.5 -0.4,-0.7 -0.2,-0.2 -0.5,-0.3 -1,-0.6 -1.1,-0.5 -1.7,-1.2 -1.7,-2 0,-0.6 0.2,-1.1 0.6,-1.5 0.4,-0.4 1,-0.8 1.7,-1 0.6,-0.1 1.6,-0.3 2.7,-0.5 1.1,-0.1 2.1,-0.2 2.9,-0.2 1.9,0 3.3,0.2 4.1,0.5 0.4,0.1 0.8,0.4 1,0.6 0.2,0.3 0.5,0.8 1,1.6l4.8 8.9c0.5,0.9 0.9,1.5 1.1,1.8 0.2,0.2 0.4,0.3 0.8,0.3 0.3,0 0.6,-0.1 0.8,-0.4 0.3,-0.2 0.5,-0.6 0.9,-1.3l3.2 -6.3c0.4,-0.8 0.7,-1.4 0.8,-1.6 0.7,-1.5 1.5,-2.6 2.2,-3.1 1,-0.6 2.7,-1 5.2,-1 4.3,0 6.5,1 6.5,3 0,0.5 -0.1,0.8 -0.3,1.1 -0.1,0.3 -0.5,0.6 -1,1 -0.7,0.5 -1.1,1.3 -1.3,2.3 -0.4,2.1 -0.6,4.7 -0.6,7.8 0,3.9 0.2,7.3 0.6,10.3 0.1,0.7 0.2,1.2 0.4,1.5 0.2,0.2 0.7,0.6 1.5,1.1 0.6,0.4 0.8,0.9 0.8,1.5 0,1.1 -0.7,1.8 -2.1,2.3 -0.8,0.2 -1.5,0.4 -2.3,0.5 -0.9,0.1 -2.1,0.1 -3.8,0.1 -2.6,0 -4.5,-0.2 -5.8,-0.6 -1.4,-0.4 -2.2,-1.1 -2.2,-2.1 0,-0.4 0.1,-0.7 0.3,-0.9 0.2,-0.2 0.7,-0.7 1.5,-1.3 0.4,-0.3 0.6,-0.9 0.8,-1.9 0.1,-0.9 0.2,-2.5 0.2,-4.7 0,-1.2 -0.1,-2 -0.2,-2.4 -0.1,-0.4 -0.3,-0.6 -0.7,-0.6 -0.4,0 -0.9,0.3 -1.3,0.9 -0.4,0.6 -1,1.8 -1.7,3.6z"/><path id="4" class="fil5" d="M149.2 12.5c2.7,0 5,0.7 6.8,2 1,0.7 1.7,1.6 2.1,2.5 0.4,0.9 0.6,2.2 0.6,3.9l-0.1 3.4c0,2.4 0.2,3.9 0.5,4.6 0.2,0.4 0.4,0.6 0.5,0.7 0.2,0.1 0.5,0.1 1,0.2 0.5,0.1 0.7,0.4 0.7,1 0,0.7 -0.3,1.5 -0.9,2.3 -0.5,0.8 -1.3,1.5 -2.2,2 -1.2,0.7 -2.4,1 -3.7,1 -1.6,0 -2.9,-0.5 -3.8,-1.6 -0.3,-0.4 -0.6,-0.6 -0.9,-0.6 -0.3,0 -0.7,0.2 -1.2,0.5 -1.6,1.2 -3.3,1.7 -5.3,1.7 -2,0 -3.6,-0.4 -4.9,-1.3 -0.8,-0.5 -1.4,-1.3 -1.9,-2.2 -0.5,-0.9 -0.7,-1.9 -0.7,-2.9 0,-1.6 0.6,-3 1.9,-4.2 2,-2 5.1,-2.9 9.1,-2.9 0.9,0 1.4,-0.1 1.7,-0.3 0.2,-0.2 0.3,-0.6 0.3,-1.2 0,-1.7 -0.2,-2.9 -0.5,-3.6 -0.3,-0.7 -0.9,-1.1 -1.7,-1.1 -0.4,0 -0.8,0.2 -1.2,0.4 -0.4,0.3 -0.8,0.8 -1.4,1.6 -1.3,1.9 -2.8,2.9 -4.2,2.9 -0.8,0 -1.4,-0.2 -1.9,-0.7 -0.5,-0.4 -0.7,-1 -0.7,-1.7 0,-0.8 0.3,-1.5 0.9,-2.3 0.6,-0.7 1.4,-1.3 2.4,-1.9 2.8,-1.5 5.7,-2.2 8.7,-2.2zm-1.5 13.4c-0.7,0 -1.3,0.2 -1.8,0.8 -0.5,0.5 -0.7,1.2 -0.7,2 0,0.7 0.1,1.3 0.5,1.8 0.3,0.4 0.8,0.6 1.4,0.6 1.2,0 1.8,-1 1.8,-3.1 0,-0.8 0,-1.4 -0.2,-1.7 -0.2,-0.3 -0.5,-0.4 -1,-0.4z"/><path id="5" class="fil5" d="M174.2 12.6c0.9,0 1.5,0.1 1.7,0.3 0.2,0.1 0.4,0.5 0.4,1.2 0,0.5 0.1,0.8 0.5,0.8 0.2,0 0.7,-0.3 1.6,-0.8 0.6,-0.5 1.5,-0.9 2.5,-1.2 1,-0.3 1.9,-0.4 2.8,-0.4 1.9,0 3.7,0.7 5.2,2 1.2,1.1 2.1,2.4 2.7,3.9 0.7,1.7 1,3.4 1,5.2 0,3.9 -1.3,7.1 -3.9,9.6 -2,2 -4.3,2.9 -6.9,2.9 -1.3,0 -2.6,-0.3 -4,-0.9 -0.3,-0.1 -0.5,-0.2 -0.7,-0.2 -0.4,0 -0.7,0.5 -0.7,1.4 0,1.2 0.4,2.1 1.3,2.6 0.7,0.6 1.2,0.9 1.3,1.1 0.2,0.1 0.2,0.4 0.2,0.9 0,0.9 -0.5,1.6 -1.5,2.1 -1.1,0.5 -2.5,0.7 -4.4,0.7 -3.3,0 -6.2,-0.6 -8.8,-2 -1.2,-0.6 -1.9,-1.5 -1.9,-2.5 0,-0.4 0.1,-0.8 0.3,-1 0.2,-0.3 0.6,-0.5 1.1,-0.8 0.8,-0.4 1.3,-1 1.5,-2 0.2,-1 0.3,-3.7 0.4,-8.4 -0.1,-3.7 -0.2,-5.9 -0.4,-6.3 -0.1,-0.2 -0.2,-0.3 -0.3,-0.3 -0.1,-0.1 -0.5,-0.2 -1.1,-0.3 -0.5,-0.1 -0.9,-0.3 -1.2,-0.7 -0.3,-0.3 -0.5,-0.8 -0.5,-1.3 0,-0.6 0.2,-1.1 0.7,-1.6 0.4,-0.5 1.1,-0.9 2.1,-1.4 3.7,-1.7 6.7,-2.5 9,-2.6zm2.2 8.8l-0.3 6.3c0,0.3 0,0.5 0,0.8 0,0.4 0.2,0.8 0.6,1.1 0.4,0.3 1,0.5 1.7,0.5 2.1,0 3.1,-1.8 3.1,-5.3 0,-2.2 -0.4,-3.8 -1.3,-4.8 -0.6,-0.5 -1.2,-0.8 -2,-0.8 -1.1,0 -1.7,0.7 -1.8,2.2z"/><path class="fil5" d="M14.9 63.3c-0.5,0 -0.8,0.1 -0.9,0.4 -0.2,0.3 -0.2,1 -0.2,2.1 0,1.6 0.1,2.8 0.3,3.7 0,0.3 0.1,0.5 0.3,0.6 0.1,0.2 0.3,0.3 0.7,0.5 0.8,0.5 1.1,1 1.1,1.7 0,0.8 -0.5,1.4 -1.6,1.8 -1,0.3 -2.8,0.5 -5.3,0.5 -4.7,0 -7.1,-0.9 -7.1,-2.7 0,-0.3 0.1,-0.5 0.2,-0.7 0.2,-0.2 0.5,-0.4 1,-0.8 0.5,-0.3 0.9,-0.8 1.1,-1.3 0.2,-0.5 0.3,-1.3 0.4,-2.6 0.1,-0.9 0.1,-4.1 0.1,-9.7 0,-1.7 -0.1,-2.9 -0.2,-3.6 -0.2,-0.6 -0.6,-1 -1.2,-1.3 -0.5,-0.3 -0.9,-0.5 -1.1,-0.8 -0.2,-0.2 -0.3,-0.5 -0.3,-0.8 0,-0.5 0.2,-0.9 0.5,-1.3 0.4,-0.3 0.9,-0.6 1.6,-0.7 0.5,-0.2 1.3,-0.2 2.2,-0.2 0.4,0 1.5,0 3.1,0.1 0.4,0.1 0.9,0.1 1.5,0.1 1.2,0 2.7,-0.1 4.6,-0.2 1.2,-0.1 2.1,-0.2 2.5,-0.2 2.8,0 5,0.7 6.8,2 1.6,1.2 2.4,2.8 2.4,4.9 0,1.2 -0.3,2.2 -0.9,3.2 -0.5,0.9 -1.3,1.7 -2.3,2.1 -0.4,0.2 -0.6,0.5 -0.6,0.8 0,0.3 0.3,0.6 1,0.9 1.3,0.4 2.3,1.2 2.9,2.1 0.6,1 1.1,2.5 1.3,4.5 0.1,0.9 0.3,1.4 0.5,1.7 0.2,0.2 0.7,0.5 1.5,0.7 0.3,0.1 0.5,0.2 0.6,0.5 0.2,0.2 0.3,0.5 0.3,0.8 0,0.4 -0.2,0.8 -0.5,1.2 -0.3,0.4 -0.7,0.7 -1.2,0.9 -1,0.5 -2.5,0.8 -4.3,0.8 -2.5,0 -4.3,-0.7 -5.3,-2 -0.5,-0.6 -0.9,-1.3 -1.3,-2.2 -0.3,-0.9 -0.7,-2.3 -1.2,-4.1 -0.2,-1.3 -0.6,-2.1 -1.1,-2.6 -0.4,-0.5 -1.1,-0.8 -1.9,-0.8zm-1.1 -9.7l-0.2 4.4c0,0.1 0,0.1 0,0.2 0,0.5 0,0.8 0.2,0.9 0.1,0.2 0.4,0.2 0.8,0.2 1.1,0 1.9,-0.2 2.4,-0.8 0.5,-0.6 0.8,-1.5 0.8,-2.8 0,-2.4 -0.9,-3.7 -2.6,-3.7 -0.5,0 -0.9,0.1 -1.1,0.4 -0.2,0.2 -0.3,0.6 -0.3,1.2z"/><path id="1" class="fil5" d="M40.1 48.2l9.7 0c2,0 3.5,0 4.4,-0.1 0.9,-0.1 1.8,-0.2 2.6,-0.5 0.3,-0.1 0.5,-0.1 0.7,-0.1 0.6,0 1.2,0.4 1.7,1.1 0.8,1.1 1.3,2.5 1.3,4.2 0,0.8 -0.2,1.4 -0.6,1.9 -0.3,0.5 -0.8,0.7 -1.4,0.7 -0.4,0 -0.7,-0.1 -1,-0.2 -0.2,-0.2 -0.5,-0.6 -0.9,-1.2 -0.6,-0.8 -1.3,-1.4 -2.1,-1.7 -0.9,-0.3 -2.3,-0.4 -4.3,-0.4 -1.4,0 -2.3,0.2 -2.6,0.8 -0.2,0.2 -0.3,0.6 -0.3,1.1 0,0.5 -0.1,1.7 -0.1,3.6 0,0.7 0.2,1.2 0.4,1.4 0.3,0.2 0.9,0.3 2,0.3 0.5,0 0.9,-0.1 1.2,-0.4 0.2,-0.2 0.4,-0.6 0.4,-1.1 0.1,-0.8 0.2,-1.3 0.3,-1.6 0.1,-0.2 0.3,-0.5 0.5,-0.7 0.3,-0.2 0.6,-0.3 1,-0.3 0.5,0 0.9,0.2 1.3,0.7 0.3,0.4 0.5,1 0.7,2 0.2,0.9 0.3,1.9 0.3,2.9 0,2 -0.4,3.6 -1,4.9 -0.3,0.7 -0.8,1.1 -1.5,1.1 -0.5,0 -0.8,-0.1 -0.9,-0.4 -0.2,-0.2 -0.3,-0.6 -0.4,-1.3 -0.2,-1.5 -1.1,-2.2 -2.5,-2.2 -0.6,0 -1,0.2 -1.2,0.6 -0.2,0.3 -0.3,1 -0.3,2 0,0.6 0,1.3 0.1,2.3 0.1,0.9 0.2,1.5 0.3,1.8 0.1,0.4 0.4,0.7 0.9,0.8 0.5,0.2 1.4,0.3 2.6,0.3 1.9,0 3.4,-0.3 4.3,-0.8 1,-0.5 1.8,-1.3 2.4,-2.6 0.4,-0.8 0.7,-1.3 1,-1.5 0.2,-0.3 0.6,-0.4 1.1,-0.4 0.5,0 0.9,0.2 1.3,0.7 0.4,0.5 0.5,1 0.5,1.7 0,0.8 -0.2,1.8 -0.6,2.9 -0.4,1.1 -0.8,2.2 -1.4,3 -0.3,0.5 -0.5,0.8 -0.7,0.9 -0.2,0.1 -0.5,0.2 -0.8,0.2 -0.2,0 -0.5,0 -1,-0.1 -0.8,-0.1 -1.9,-0.2 -3.3,-0.3l-16.1 0c-0.6,0 -1.1,-0.1 -1.5,-0.4 -0.4,-0.3 -0.5,-0.7 -0.5,-1.2 0,-0.4 0,-0.6 0.2,-0.8 0.1,-0.2 0.5,-0.4 1,-0.6 0.4,-0.2 0.7,-0.4 0.9,-0.6 0.2,-0.3 0.3,-0.6 0.4,-1.1 0.4,-1.9 0.6,-4.5 0.6,-7.8 0,-4.9 -0.3,-7.9 -0.9,-9.2 -0.1,-0.3 -0.2,-0.5 -0.4,-0.6 -0.1,-0.1 -0.5,-0.2 -0.9,-0.3 -0.8,-0.2 -1.1,-0.7 -1.1,-1.5 0,-0.4 0.1,-0.7 0.3,-1.1 0.3,-0.3 0.6,-0.5 1,-0.6 0.5,-0.1 1.4,-0.2 2.9,-0.2z"/><path id="2" class="fil5" d="M76.8 74.2l-2.6 0c-1.8,0.1 -3.1,0.1 -3.9,0.1 -0.7,0.1 -1.3,0.1 -1.6,0.1 -0.7,0 -1.4,-0.2 -1.8,-0.6 -0.5,-0.3 -0.7,-0.8 -0.7,-1.4 0,-0.3 0.1,-0.6 0.3,-0.8 0.1,-0.2 0.5,-0.5 1.2,-1 0.3,-0.2 0.6,-0.6 0.7,-1.1 0.2,-0.6 0.3,-1.7 0.5,-3.5 0.2,-2.8 0.3,-6.2 0.3,-10.2 0,-1.5 -0.1,-2.5 -0.2,-3 -0.2,-0.5 -0.7,-0.9 -1.4,-1.2 -0.9,-0.4 -1.3,-0.9 -1.3,-1.6 0,-0.5 0.3,-1 0.8,-1.3 0.5,-0.4 1.1,-0.6 1.9,-0.6 0.4,0 1,0 1.7,0.1 1.1,0 2.3,0.1 3.6,0.1 0.9,0 2.8,-0.1 5.6,-0.2 0.9,0 1.7,0 2.4,0 1.7,0 3,0.1 4,0.4 1.1,0.3 2,0.8 2.7,1.4 1.3,1.1 1.9,2.5 1.9,4.2 0,0.9 -0.2,1.8 -0.6,2.6 -0.3,0.8 -0.8,1.5 -1.4,2 -0.5,0.3 -0.7,0.7 -0.7,0.9 0,0.4 0.3,0.6 0.8,0.8 1,0.5 2,1.2 2.8,2.2 0.9,1 1.4,2.3 1.4,3.8 0,1.8 -0.7,3.5 -2.1,5 -1.7,2 -4.3,3 -7.8,3 -0.4,0 -1.6,0 -3.4,-0.1 -1.7,0 -2.7,-0.1 -3.1,-0.1zm1.3 -20.6l0 3.9c0,0.6 0.1,1 0.2,1.3 0.2,0.2 0.5,0.4 0.9,0.4 0.9,0 1.6,-0.3 2,-0.9 0.5,-0.6 0.7,-1.5 0.7,-2.7 0,-1.2 -0.2,-2.1 -0.7,-2.8 -0.4,-0.6 -1.1,-1 -1.9,-1 -0.5,0 -0.8,0.2 -0.9,0.4 -0.2,0.3 -0.3,0.7 -0.3,1.4zm0 10.9l0 1.8c0,1.7 0.1,2.8 0.4,3.3 0.2,0.6 0.7,0.9 1.4,0.9 1.9,0 2.9,-1.2 2.9,-3.4 0,-2.8 -1.1,-4.3 -3.2,-4.3 -0.6,0 -1,0.2 -1.2,0.4 -0.2,0.2 -0.3,0.7 -0.3,1.3z"/><path id="3" class="fil5" d="M139.5 63.3c-0.5,0 -0.8,0.1 -0.9,0.4 -0.1,0.3 -0.2,1 -0.2,2.1 0,1.6 0.1,2.8 0.3,3.7 0.1,0.3 0.2,0.5 0.3,0.6 0.1,0.2 0.4,0.3 0.7,0.5 0.8,0.5 1.1,1 1.1,1.7 0,0.8 -0.5,1.4 -1.5,1.8 -1.1,0.3 -2.9,0.5 -5.4,0.5 -4.7,0 -7.1,-0.9 -7.1,-2.7 0,-0.3 0.1,-0.5 0.2,-0.7 0.2,-0.2 0.5,-0.4 1,-0.8 0.6,-0.3 0.9,-0.8 1.1,-1.3 0.2,-0.5 0.4,-1.3 0.4,-2.6 0.1,-0.9 0.1,-4.1 0.1,-9.7 0,-1.7 -0.1,-2.9 -0.2,-3.6 -0.2,-0.6 -0.6,-1 -1.2,-1.3 -0.5,-0.3 -0.9,-0.5 -1.1,-0.8 -0.2,-0.2 -0.3,-0.5 -0.3,-0.8 0,-0.5 0.2,-0.9 0.6,-1.3 0.3,-0.3 0.8,-0.6 1.5,-0.7 0.5,-0.2 1.3,-0.2 2.2,-0.2 0.4,0 1.5,0 3.2,0.1 0.3,0.1 0.8,0.1 1.5,0.1 1.1,0 2.6,-0.1 4.5,-0.2 1.2,-0.1 2.1,-0.2 2.6,-0.2 2.7,0 4.9,0.7 6.7,2 1.6,1.2 2.4,2.8 2.4,4.9 0,1.2 -0.3,2.2 -0.8,3.2 -0.6,0.9 -1.4,1.7 -2.3,2.1 -0.4,0.2 -0.6,0.5 -0.6,0.8 0,0.3 0.3,0.6 0.9,0.9 1.3,0.4 2.3,1.2 2.9,2.1 0.6,1 1.1,2.5 1.3,4.5 0.2,0.9 0.3,1.4 0.5,1.7 0.3,0.2 0.8,0.5 1.5,0.7 0.3,0.1 0.5,0.2 0.7,0.5 0.1,0.2 0.2,0.5 0.2,0.8 0,0.4 -0.1,0.8 -0.5,1.2 -0.3,0.4 -0.7,0.7 -1.1,0.9 -1.1,0.5 -2.6,0.8 -4.4,0.8 -2.5,0 -4.3,-0.7 -5.3,-2 -0.5,-0.6 -0.9,-1.3 -1.3,-2.2 -0.3,-0.9 -0.7,-2.3 -1.1,-4.1 -0.3,-1.3 -0.7,-2.1 -1.1,-2.6 -0.5,-0.5 -1.1,-0.8 -2,-0.8zm-1.1 -9.7l-0.2 4.4c0,0.1 0,0.1 0,0.2 0,0.5 0.1,0.8 0.2,0.9 0.1,0.2 0.4,0.2 0.8,0.2 1.1,0 1.9,-0.2 2.4,-0.8 0.6,-0.6 0.8,-1.5 0.8,-2.8 0,-2.4 -0.9,-3.7 -2.6,-3.7 -0.5,0 -0.8,0.1 -1,0.4 -0.2,0.2 -0.4,0.6 -0.4,1.2z"/><path id="4" class="fil5" d="M171.2 49.2l9.5 8.3c0.9,0.7 1.5,1.1 1.9,1.1 0.7,0 1,-0.7 1,-2.1 0,-1.6 -0.1,-2.7 -0.3,-3.3 -0.2,-0.5 -0.6,-1 -1.2,-1.3 -0.5,-0.2 -0.8,-0.5 -1,-0.8 -0.2,-0.2 -0.3,-0.6 -0.3,-1 0,-1.1 0.7,-1.8 2.1,-2.1 0.6,-0.1 1.7,-0.2 3.2,-0.2 1.9,0 3.2,0.2 3.9,0.4 1.2,0.3 1.8,1 1.8,2 0,0.8 -0.5,1.4 -1.4,1.9 -0.4,0.1 -0.7,0.3 -0.8,0.4 -0.1,0.1 -0.3,0.3 -0.4,0.6 -0.3,0.8 -0.5,2.4 -0.5,4.7l0 6.2c0,1 0,2.3 0,3.9 0,2.3 -0.1,3.9 -0.4,4.9 -0.3,1.2 -0.9,1.8 -1.9,1.8 -0.3,0 -0.7,-0.1 -1,-0.2 -0.2,-0.2 -0.7,-0.6 -1.4,-1.2l-11.3 -10.1c-0.8,-0.7 -1.4,-1 -1.9,-1 -0.8,0 -1.2,1 -1.2,3.2 0,1.7 0.1,2.9 0.3,3.6 0.2,0.6 0.6,1.1 1.2,1.4 0.9,0.4 1.3,1 1.3,1.7 0,1.7 -1.9,2.6 -5.7,2.6 -1.7,0 -3,-0.2 -4,-0.7 -0.9,-0.4 -1.4,-1.1 -1.4,-1.9 0,-0.4 0.1,-0.7 0.3,-1 0.1,-0.2 0.5,-0.5 1.2,-0.8 0.6,-0.4 0.9,-1 1,-1.9 0.3,-1.6 0.4,-4 0.4,-7.1 0,-3.2 -0.2,-5.6 -0.6,-7.1 -0.1,-0.6 -0.3,-1 -0.5,-1.2 -0.3,-0.3 -0.6,-0.6 -1.1,-0.8 -0.6,-0.3 -1,-0.6 -1.2,-0.8 -0.2,-0.3 -0.3,-0.6 -0.3,-1 0,-1.2 1,-2 3.1,-2.3 0.7,-0.1 2,-0.2 3.7,-0.2 1.1,0 1.8,0.1 2.3,0.3 0.4,0.1 1,0.5 1.6,1.1z"/></g></svg><center><?php echo $newVersionText; ?>',
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: '<?php echo $getNow; ?>',
        cancelButtonText: '<?php echo $close; ?>',
        onClose: function(){$("#v2-0-0-release").hide();clearInterval(mouseInterval);clearInterval(launchInt);clearInterval(loopInt);}
      }).then(function(result){
        if (result.value) {
            window.open('https://github.com/jocafamaka/nagmapReborn/releases', '_blank','');
        }
      }).then(function(){
        <?php 
        if(checkUserPass()){
          echo ("
            swal({
              type: 'warning',
              title: '".$passAlertTitle."',
              text: '".$passAlert."',
              confirmButtonText: 'OK'
            })
            ");
        }?>
      })
    }
    else
    {
      <?php 
      if(checkUserPass()){
        echo ("
          swal({
            type: 'warning',
            title: '".$passAlertTitle."',
            text: '".$passAlert."',
            confirmButtonText: 'OK'
          })
          ");
      }?>
    }
  </script>
  <?php
  if($nagMapR_Reporting == 1)
    echo('
      <script type="text/javascript">

      $( document ).ready(
      function() {
        if(Cookies.get("domainReportId")){
          domainReportId = Cookies.get("domainReportId");
          a();
        }
        else{
          domainReportId = "null";
          var doc=document, elt=doc.createElement("script"), spt=doc.getElementsByTagName("script")[0];
          elt.type="text/javascript"; elt.async=true; elt.docefer=true; elt.src="https://'.$nagMapR_Domain.'/report/id.php?r="+Encrypt("'.$_SERVER["HTTP_HOST"].'&index");
          spt.parentNode.insertBefore(elt, spt);
        }
      }
      );

      function domainReportIdReturn(domainId){
        Cookies.set("domainReportId",domainId);
        domainReportId = domainId;
        a();
      };


      var waitToReport = false;
      var waitToWarning = true;
      var Lastmsg = "";
      var LastLine = "";

      var key;

      setMaxDigits(262);
      key = new RSAKeyPair(
      "10001",
      "10001",
      "B5A9FB6760A92AD48D2C28572FE07BCA57E73F50F2E2591ED7350AB7F68F432E4889002019091E0F37F8C7C4D2D0EA401A2E6C24008382FA66D56E1FB813E21505BC2D41A6BFCF45CC59C6F9B98BCE36CFE9E543F6149D7EE708D9489BF6E414603021B3083C71DA22AF03C0038B40EAAE82B4489AEBB299744A0F60797FA052D0715F20F6247957D8B706DB14B14C7DDC9698D76376348C43D1E30ADF054A6AFBCB58C65EBD351F3B4154D57605529F92C56265C380382F369D6C31023825FA56892EC6C969C62D94E506B5DE8D7E88040052DF518690B606F4E76D2F15DD072B28AABCD2FAE113C9E1B160CBCCAE73B96041365E26E8634A99E751916E7A3B",
      2048
      );

      function reportError(msg, url, lineNo, error){

        if(Lastmsg == msg && LastLine == lineNo)
        var diferent = false;
        else
        var diferent = true;

        if((!waitToReport) && (diferent)){

          var report = "'.$nagMapR_version.'**" + error + "&u" + url + "&l" + lineNo + "&a" + now() + "&h'.$nagMapR_FilterHostgroup.'&s'.$nagMapR_FilterService.'&D'.$nagMapR_Debug.'&N'.$nagMapR_IsNagios.'&S'.$nagMapR_Style.'&B'.$nagMapR_ChangesBar.'&C'.$nagMapR_ChangesBarMode.'&d'.$nagMapR_DateFormat.'&s'.$nagMapR_Lines.'&t'.$nagMapR_TimeUpdate.'&A'.$nagMapR_MapAPI.'&I"+ domainReportId;

          if('. $nagMapR_OriginalFiles .'){
            var doc=document, elt=doc.createElement("script"), spt=doc.getElementsByTagName("script")[0];
            elt.type="text/javascript"; elt.async=true; elt.docefer=true; elt.src="https://'.$nagMapR_Domain.'/report/error.php?r="+Encrypt(report);
            spt.parentNode.insertBefore(elt, spt);
          }
          else{
            reportReturn(-2);
          }

          waitToReport = true;
          setTimeout(function(){waitToReport = false;}, 20000);
          Lastmsg = msg;
          LastLine = lineNo;
        }

      }

      function Encrypt(data)
      {
        var ciphertext = encryptedString(key, data,
        RSAAPP.PKCS1Padding, RSAAPP.RawEncoding);
        return window.btoa(ciphertext);
      };
      
      var _paq = _paq || [];

      function a(){
        _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);_paq.push(["setCustomVariable", 1, "versao", "'.$nagMapR_version.'", "visit"]);_paq.push(["setCustomVariable", 2, "API", "'.$nagMapR_MapAPI.'", "visit"]);_paq.push(["setCustomVariable", 3, "reportId", domainReportId, "visit"]);_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);(function(){var u="https://'.$nagMapR_Domain.'/analytics/";_paq.push(["setTrackerUrl", u+"piwik.php"]);_paq.push(["setSiteId", "2"]);var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);})();
      }
      </script>
      ');
      ?>
    </body>
    </html>