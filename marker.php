<?php
include_once("functions.php");

include_once('config.php');

include_once("langs/$nagMapR_Lang.php");

//Auth Request
require_auth();

// pre-define variables so the E_NOTICES do not show in webserver logs
$javascript = "";

// Get list of all Nagios configuration files into an array
$files = get_config_files();

// Read content of all Nagios configuration files into one huge array
foreach ($files as $file) {
  $raw_data[$file] = file($file);
}

$data = filter_raw_data($raw_data, $files);

// hosts definition - we are only interested in hostname, parents and notes with position information
foreach ($data as $host) {
  if (((!empty($host["host_name"])) && (!preg_match("/^\\!/", $host['host_name']))) | ($host['register'] == 0)) {
    $hostname = 'x'.safe_name($host["host_name"]).'x';
    $hosts[$hostname]['host_name'] = $hostname;
    $hosts[$hostname]['nagios_host_name'] = $host["host_name"];
    $hosts[$hostname]['alias'] = $host["alias"];

    // iterate for every option for the host
    foreach ($host as $option => $value) {
      // get parents information
      if ($option == "parents") {
        $parents = explode(',', $value); 
        foreach ($parents as $parent) {
          $parent = safe_name($parent);
          $hosts[$hostname]['parents'][] = "x".$parent."x";
        }
        continue;
      }
      // we are only interested in latlng values from notes
      if ($option == "notes") {
        if (preg_match("/latlng/",$value)) { 
          $value = explode(":",$value); 
          $hosts[$hostname]['latlng'] = trim($value[1]);
          continue;
        } else {
          continue;
        }
      };
      // another few information we are interested in
      if (($option == "address")) {
        $hosts[$hostname]['address'] = trim($value);
      };
      if (($option == "hostgroups")) {
        $hostgroups = explode(',', $value);
        foreach ($hostgroups as $hostgroup) {
          $hosts[$hostname]['hostgroups'][] = $hostgroup;
        }
      };
      // another few information we are interested in - this is a user-defined nagios variable
      if (preg_match("/^_/", trim($option))) {
        $hosts[$hostname]['user'][] = $option.':'.$value;
      };
      unset($parent, $parents);
    } 
  }
}
unset($data);

if ($nagMapR_FilterHostgroup) {
  foreach ($hosts as $host) {
    if (!in_array($nagMapR_FilterHostgroup, $hosts[$host["host_name"]]['hostgroups'])) {
      unset($hosts[$host["host_name"]]);
    }
  }
}

// get host statuses
$s = nagMapR_status();

$ii = 0;
// remove hosts we are not able to render and combine those we are able to render with their statuses 
foreach ($hosts as $h) {
  if ((isset($h["latlng"])) AND (isset($h["host_name"])) AND (isset($s[$h["nagios_host_name"]]['status']))) {
    $data[$h["host_name"]] = $h;
    $data[$h["host_name"]]['status'] = $s[$h["nagios_host_name"]]['status'];
    $jsData[$ii]['host_name'] = $h['host_name'];
    $jsData[$ii]['time'] = $s[$h["nagios_host_name"]]['time'];
    $jsData[$ii]['nagios_host_name'] = $h['nagios_host_name'];
    $jsData[$ii]['alias'] = $h['alias'];
    if(is_array($h["parents"]))
      $jsData[$ii]['parents'] = $h['parents'];
    $jsData[$ii]['status'] = $s[$h["nagios_host_name"]]['status'];
    $ii++;
  }
}
unset($hosts);
unset($s);

$fil = ($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3 && $nagMapR_BarFilter == 1) ? (' onclick=\"filterBy(this);\" class=\"filter\"') : ("");
$tip = ($nagMapR_ChangesBar == 1 && $nagMapR_ChangesBarMode !=3 && $nagMapR_BarFilter == 1) ? (' tooltip=\"'.$asFilter.'\" tooltip-position=\"buttom\"') : ("");

if($nagMapR_MapAPI == 0){

  $ii = 0;
  // put markers and bubbles onto a map
  foreach ($data as $h) {
    // position the host on the map
    $javascript .= ("window.".$h["host_name"]."_pos = new google.maps.LatLng(".$h["latlng"].");\n");

    // display different icons for the host (according to the status in nagios)
    // if host is in state OK
    if ($h['status'] == 0) {
      $javascript .= ("MARK.push(new google.maps.Marker({ position: ".$h["host_name"]."_pos, icon: iconGreen, map: map, zIndex: 2, title: \"".$h["nagios_host_name"]."\"}));"."\n\n");
    // if host is in state UP but in WARNING
    } elseif ($h['status'] == 1) {
      $javascript .= ("MARK.push(new google.maps.Marker({ position: ".$h["host_name"]."_pos, icon: iconYellow, map: map, zIndex: 3, title: \"".$h["nagios_host_name"]."\"}));"."\n\n");
    // if host is in state UP but CRITICAL
    }elseif ($h['status'] == 2) {
      $javascript .= ("MARK.push(new google.maps.Marker({ position: ".$h["host_name"]."_pos, icon: iconOrange, map: map, zIndex: 4, title: \"".$h["nagios_host_name"]."\"}));"."\n\n");
    // if host is in state DOWN
    } elseif ($h['status'] == 3) {
      $javascript .= ("MARK.push(new google.maps.Marker({ position: ".$h["host_name"]."_pos, icon: iconRed, map: map, zIndex: 5, title: \"".$h["nagios_host_name"]."\"}));"."\n\n");
    } else {
    // if host is in state UNKNOWN
      $javascript .= ("window.MARK.push(new google.maps.Marker({ position: ".$h["host_name"]."_pos, icon: iconGrey, map: map, zIndex: 2, title: \"".$h["nagios_host_name"]."\"}));"."\n\n");
    };
    //generate google maps info bubble
    if (!isset($h["parents"])) { $h["parents"] = Array(); }; 
    $info = '<div class=\"bubble\"><strong>'.$h["nagios_host_name"]."</strong><br><table>"
    .'<tr'.$fil.'><td>'.$alias.'</td><td>:</td><td'.$tip.'> '.$h["alias"].'</td></tr>'
    .'<tr><td>'.$hostG.'</td><td>:</td><td> '.join('<br>', $h["hostgroups"]).'</td></tr>'
    .'<tr><td>'.$addr.'</td><td>:</td><td> '.$h["address"].'</td></tr>'
    .'<tr><td>'.$other.'</td><td>:</td><td> '.join("<br>",$h['user']).'</td></tr>'
    .'<tr><td>'.$hostP.'</td><td>:</td><td> '.join('<br>' , $h["parents"]).'</td></tr>'
    .'</table>';

    if($nagMapR_IsNagios == 1){
      $info .='<a href=\"/nagios/cgi-bin/statusmap.cgi\?host='.$h["nagios_host_name"].'\">Nagios map page</a>'
      .'<br><a href=\"/nagios/cgi-bin/extinfo.cgi\?type=1\&host='.$h["nagios_host_name"].'\">Nagios host page</a>'
      .'<center><a href="https://www.github.com/jocafamaka/nagmapReborn/" target="_blank"><img title="'. $project .'" src="resources/img/logoMiniBlack.png" alt=""></a><center>';
    }
    else{
      $info .= '<center><a href="https://www.github.com/jocafamaka/nagmapReborn/"><img title="'. $project .'" src="resources/img/logoMiniBlack.png" alt=""></a><center>';

    }

    $info = preg_replace("#[']#", "\\'", $info);

    $javascript .= ("INFO.push(new google.maps.InfoWindow({ content: '$info'}));\n");

    $javascript .= ("google.maps.event.addListener(MARK[".$ii."], 'click', function() {openPopup(".$ii.", false);\n});\n\n");
    $ii++;
  };

  if($nagMapR_Lines == 1){
    $ii = 0;
    // create (multiple) parent connection links between nodes/markers
    $javascript .= "\n\n// generating links between hosts\n";
    foreach ($data as $h) {
      // if we do not have any parents, just create an empty array
      if (!isset($h["latlng"]) OR (!is_array($h["parents"]))) {
        continue;
      }
      foreach ($h["parents"] as $parent) {
        if (isset($data[$parent]["latlng"])) {
          // default colors for links
          if ($h['status'] == 0)
            $stroke_color = "#007f00";
          // links in warning state
          elseif ($h['status'] == 1) 
            $stroke_color ='#ffff00';
            // links in critical state
          elseif ($h['status'] == 2) 
            $stroke_color ='#d25700';
          // links in problem state
          elseif ($h['status'] == 3)
            $stroke_color ='#c92a2a';
          else
            $stroke_color ='#A9ABAE';
          $javascript .= "\n";

          $linesArray .= ("LINES.push({line: null, host:\"".$h["host_name"]."\", parent:\"".$parent."\"});\n");

          $javascript .= ('createLine('.$ii.', '.$h["host_name"].'_pos, '.$parent.'_pos, "'.$stroke_color.'", map);');

          $ii++;
        }
      }
    }
  }
}
else{
  // put markers and bubbles onto a map
  foreach ($data as $h) {
    // position the host on the map
    $javascript .= ("window.".$h["host_name"]."_pos = new L.latLng(".$h["latlng"].");\n");

    // display different icons for the host (according to the status in nagios)
    // if host is in state OK
    if ($h['status'] == 0) {
      $javascript .= ("MARK.push(L.marker(".$h["host_name"]."_pos, {icon: iconGreen, zIndexOffset: 2000}).addTo(map).bindPopup(\"");
      // if host is in state UP but in WARNING
    } elseif ($h['status'] == 1) {
      $javascript .= ("MARK.push(L.marker(".$h["host_name"]."_pos, {icon: iconYellow, zIndexOffset: 3000}).addTo(map).bindPopup(\"");
      // if host is in state UP but CRITICAL
    }elseif ($h['status'] == 2) {
      $javascript .= ("MARK.push(L.marker(".$h["host_name"]."_pos, {icon: iconOrange, zIndexOffset: 4000}).addTo(map).bindPopup(\"");
      // if host is in state DOWN
    } elseif ($h['status'] == 3) {
      $javascript .= ("MARK.push(L.marker(".$h["host_name"]."_pos, {icon: iconRed, zIndexOffset: 5000}).addTo(map).bindPopup(\"");
      // if host is in state UNKNOWN
    } else {
      $javascript .= ("MARK.push(L.marker(".$h["host_name"]."_pos, {icon: iconGrey, zIndexOffset: 2000}).addTo(map).bindPopup(\"");
    };
    //generate google maps info bubble
    if (!isset($h["parents"])) { $h["parents"] = Array(); }; 
    $info = '<div class=\"bubble\"><strong>'.$h["nagios_host_name"]."</strong><br><table>"
    .'<tr'.$fil.'><td>'.$alias.'</td><td>:</td><td'.$tip.'> '.$h["alias"].'</td></tr>'
    .'<tr><td>'.$hostG.'</td><td>:</td><td> '.join('<br>', $h["hostgroups"]).'</td></tr>'
    .'<tr><td>'.$addr.'</td><td>:</td><td> '.$h["address"].'</td></tr>'
    .'<tr><td>'.$other.'</td><td>:</td><td> '.join("<br>",$h['user']).'</td></tr>'
    .'<tr><td>'.$hostP.'</td><td>:</td><td> '.join('<br>' , $h["parents"]).'</td></tr>'
    .'</table>';

    if($nagMapR_IsNagios == 1){
      $info .='<a href=\"/nagios/cgi-bin/statusmap.cgi\?host='.$h["nagios_host_name"].'\">Nagios map page</a>'
      .'<br><a href=\"/nagios/cgi-bin/extinfo.cgi\?type=1\&host='.$h["nagios_host_name"].'\">Nagios host page</a>'
      .'<center><a href=\"https://github.com/jocafamaka/nagmapReborn-leaflet\" target=\"_blank\"><img title=\"'. $project .'\" src=\"resources/img/logoMiniBlack.png\" alt=\"\"></a><center>';
    }
    else{
      $info .= '<center><a href=\"https://github.com/jocafamaka/nagmapReborn\"><img title=\"'. $project .'\" src=\"resources/img/logoMiniBlack.png\" alt=\"\"></a><center>';

    }

    $javascript .= ($info."\"));");

  };

  if($nagMapR_Lines == 1){
    $ii = 0;
    // create (multiple) parent connection links between nodes/markers
    $javascript .= "\n\n// generating links between hosts\n";
    foreach ($data as $h) {
      // if we do not have any parents, just create an empty array
      if (!isset($h["latlng"]) OR (!is_array($h["parents"]))) {
        continue;
      }
      foreach ($h["parents"] as $parent) {
        if (isset($data[$parent]["latlng"])) {
          // default colors for links
          if ($h['status'] == 0)
            $stroke_color = "#007f00";
          // links in warning state
          elseif ($h['status'] == 1) 
            $stroke_color ='#ffff00';
          // links in critical state
          elseif ($h['status'] == 2) 
            $stroke_color ='#d25700';
          // links in problem state
          elseif ($h['status'] == 3)
            $stroke_color ='#c92a2a';
          else
            $stroke_color ='#A9ABAE';
          $javascript .= "\n";

          $linesArray .= ("LINES.push({line: null, host:\"".$h["host_name"]."\", parent:\"".$parent."\"});\n");

          $javascript .= ('createLine('.$ii.', '.$h["host_name"].'_pos,'.$parent.'_pos, "'.$stroke_color.'", map);');

          $ii++;
        }
      }
    }
  }
}

?>
