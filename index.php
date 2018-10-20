<?php
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
$nagMapR_version = file_get_contents('VERSION');
$nagMapR_CurrVersion = file_get_contents('https://raw.githubusercontent.com/jocafamaka/nagmapReborn/master/VERSION');
if($nagMapR_CurrVersion == "")  //Set local version in case of fail.
$nagMapR_CurrVersion = $nagMapR_version;

include('validateAndVerify.php');

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
      <script type="text/javascript" src="resources/leaflet/leaflet.smoothmarkerbouncing.js"></script>
      ');
  }
  ?>
  

  <script type="text/javascript">

    // Defines the array used for the comparisons.
    var hostStatus = <?php echo json_encode($jsData); ?>;

    <?php
    if($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode == 2)
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
      echo ("var audio = new Audio('resources/Beep.mp3');\n");

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

          var map = L.map('map',{zoomControl:false}).setView([".$nagMapR_MapCentre."], ".$nagMapR_MapZoom.");

          L.tileLayer('".$nagMapR_LeafletStyle."', {attribution:'&copy; Contribuidores do <a href=\"http://osm.org/copyright\">OpenStreetMap</a>'}).addTo(map);
          ");
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
    echo ('};'); //end of initialize function
    echo ('
      </script>
      </head>
      <body style="margin:0px; padding:0px; overflow:hidden;" onload="initialize()">');
    if ($nagMapR_Debug == 1){
      echo ('<a href="debugInfo/index.php"><div href="debugInfo/index.php" id="div_fixa" class="div_fixa" style="z-index:2000;"><button class="button" style="vertical-align:middle"><span>Debug page</span></button></div></a>');
    }
    if ($nagMapR_ChangesBar == 1) {
      echo '<div id="map" style="width:100%; height:'.(100-$nagMapR_ChangesBarSize).'%; float: left"></div>';
      echo '<div id="changesbar" style="padding-top:2px; padding-left: 1px; background: black; height:'.$nagMapR_ChangesBarSize.'%; overflow:auto;">';
      if($nagMapR_ChangesBarMode == 2){
        echo('<div id="downHosts"></div><div id="critHosts"></div><div id="warHosts"></div>');
      }
      echo('</div>');
    } else {
      echo '<div id="map" style="width:100%; height:100%; float: left"></div>';
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

    function changeLines(host, color){
      if(Array.isArray(hostStatus[host].parents)){
        for (var i = hostStatus[host].parents.length - 1; i >= 0; i--) {
          for (var ii = LINES.length - 1; ii >= 0; ii--) {
            if( (hostStatus[host].host_name == LINES[ii].host) && (hostStatus[host].parents[i] == LINES[ii].parent))
              <?php
            if($nagMapR_MapAPI == 0)
              echo("LINES[ii].line.setOptions({strokeColor: color});\n");
            else
              echo("LINES[ii].line.setStyle({color: color});\n");
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
            time = 15;
          if(MARK[host].isBouncing())
            MARK[host].stopBouncing();
          else
            MARK[host].bounce(time);
          \n");
      }
      ?>
    };

    function updateStatus(host, status){  // Updates the status of the host informed and apply the animations

      if(status == 0){

        <?php
        if($nagMapR_ChangesBar == 1){
          if($nagMapR_ChangesBarMode == 1){
            echo ('
              var newUp = ("<div class=\"changesBarLine UP\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

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
              var newUp = ("<div class=\"changesBarLine WAR\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

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
              var newUp = ("<div class=\"changesBarLine CRIT\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

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
              var newUp = ("<div class=\"changesBarLine DOWN\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

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

              var newUp = ("<div class=\"changesBarLine UNK\" style=\"font-size:'. $nagMapR_FontSize .'px;\">" + now() + " - " + hostStatus[host].alias + ": ");

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

        changeIcon(host, iconGrey, 0, 3);

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

            var insert = ("<div class=\"changesBarLine " + status + " news\" id=\"" + hostStatus[i].nagios_host_name + "-" + status + "\" style=\"font-size: '. $nagMapR_FontSize .'px; opacity:0; max-height: 0px;\">" + hostStatus[i].alias + " - '. $timePrefix .'" + time + "'. $timeSuffix .'</div>");

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
              document.getElementById(\'warHosts\').insertAdjacentHTML("afterbegin", "<div class=\"changesBarLine WAR news\" id=\"" + name + "-WAR\"" + insert);
            }
            if(hostStatusPre[i].status == 2){
              document.getElementById(\'critHosts\').insertAdjacentHTML("afterbegin", "<div class=\"changesBarLine CRIT news\" id=\"" + name + "-CRIT\"" + insert);
            }
            if(hostStatusPre[i].status == 3){
              document.getElementById(\'downHosts\').insertAdjacentHTML("afterbegin", "<div class=\"changesBarLine DOWN news\" id=\"" + name + "-DOWN\"" + insert);
            }
          }

          hostStatusPre = [];
          ');
      }
    }
    ?>

    setInterval(function(){ // Request the arrau with the update status of each host.

      var ajax = new XMLHttpRequest();

      var arrayHosts;

      ajax.open('GET', 'update.php?key=<?php echo $nagMapR_key ?>', true);

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

          if(Lastmsg == msg && LastLine == lineNo)
            var diferent = false;
          else
            var diferent = true;

          if((!waitToReport) && (diferent)){

            var report = "'.$nagMapR_version.'**" + error + "&u" + url + "&l" + lineNo + "&a" + now() + "&h'. $nagMapR_FilterHostgroup. '&s'. $nagMapR_FilterService. '&D'. $nagMapR_Debug. '&N'. $nagMapR_IsNagios. '&S'. $nagMapR_Style. '&B'. $nagMapR_ChangesBar. '&C'. $nagMapR_ChangesBarMode. '&d'. $nagMapR_DateFormat. '&s'. $nagMapR_Lines. '&t'. $nagMapR_TimeUpdate. '";

            var doc=document, elt=doc.createElement("script"), spt=doc.getElementsByTagName("script")[0];
            elt.type="text/javascript"; elt.async=true; elt.docefer=true; elt.src="//report.nagmapreborn.com/error.php?r="+Encrypt(report);
            spt.parentNode.insertBefore(elt, spt);

            waitToReport = true;
            setTimeout(function(){waitToReport = false;}, 20000);
            Lastmsg = msg;
            LastLine = lineNo;
          }
        }
        ');
      else
        echo ('}');
    }
    else{
      if($nagMapR_Reporting == 1)
        echo ('

          window.onerror = function (msg, url, lineNo, columnNo, error) {

            if(Lastmsg == msg && LastLine == lineNo)
              var diferent = false;
            else
              var diferent = true;

            if((!waitToReport) && (diferent)){

              var report = "'.$nagMapR_version.'**" + error + "&u" + url + "&l" + lineNo + "&a" + now() + "&h'. $nagMapR_FilterHostgroup. '&s'. $nagMapR_FilterService. '&D'. $nagMapR_Debug. '&N'. $nagMapR_IsNagios. '&S'. $nagMapR_Style. '&B'. $nagMapR_ChangesBar. '&C'. $nagMapR_ChangesBarMode. '&d'. $nagMapR_DateFormat. '&s'. $nagMapR_Lines. '&t'. $nagMapR_TimeUpdate. '";

              var doc=document, elt=doc.createElement("script"), spt=doc.getElementsByTagName("script")[0];
              elt.type="text/javascript"; elt.async=true; elt.docefer=true; elt.src="//report.nagmapreborn.com/error.php?r="+Encrypt(report);
              spt.parentNode.insertBefore(elt, spt);

              waitToReport = true;
              setTimeout(function(){waitToReport = false;}, 20000);
              Lastmsg = msg;
              LastLine = lineNo;
            }
          }
          ');
    }
    ?>
  </script>
  <script src="debugInfo/resources/js/jquery.min.js"></script>
  <script src="resources/toastr/toastr.min.js"></script>
  <script src="resources/sa/sweetalert2.all.min.js"></script>
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

    function reportReturn(type){
      toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "showDuration": "300",
        "timeOut": "20000",
        "extendedTimeOut": "10000",
      };

      if(type < 1)
      {
        toastr["info"]("<?php echo ($reporterErrorPre) ?>");
        if(type == -1)
          toastr["error"]("<?php echo ($reporterError) ?>");
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
      swal({
        type: 'info',
        title: '<?php echo $newVersion; ?> (<?php echo $nagMapR_CurrVersion; ?>)',
        html: '<?php echo $newVersionText; ?><center><a href="https://github.com/jocafamaka/nagmapReborn/releases" target="_blank" style="cursor: pointer;"><img title="<?php echo $project; ?>" src="resources/img/logoBlack.svg" alt=""></a><center>',
        confirmButtonText: '<?php echo $close; ?>'
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
      var waitToReport = false;
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

      function Encrypt(data)
      {
        var ciphertext = encryptedString(key, data,
        RSAAPP.PKCS1Padding, RSAAPP.RawEncoding);
        return window.btoa(ciphertext);
      };

      var _paq = _paq || [];_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);_paq.push(["setCustomVariable", 1, "versao", "'.$nagMapR_version.'", "visit"]);_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);(function(){var u="//analytics.nagmapreborn.com/";_paq.push(["setTrackerUrl", u+"piwik.php"]);_paq.push(["setSiteId", "2"]);var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);})();
      </script>
      ');
      ?>
    </body>
    </html>
