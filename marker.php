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

include("functions.php");
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
    $jsData[$ii]['nagios_host_name'] = $h['nagios_host_name'];
    $jsData[$ii]['alias'] = $h['alias'];
    if(is_array($h["parents"]))
      $jsData[$ii]['parents'] = $h['parents'];
    $jsData[$ii]['status'] = $s[$h["nagios_host_name"]]['status'];
    $ii++;
  }
  else {
    if ($nagMapR_Debug) { 
      echo('// '.$ignoredHosts.$h['host_name'].":".$h['latlng'].":".$s[$h["nagios_host_name"]]['status_human']." - Reason:");
      if(!isset($h["latlng"]))
        echo(" (LatLng)");
      if(!isset($h["host_name"]))
        echo(" (HostName)");
      if(!isset($s[$h["nagios_host_name"]]['status']))
        echo(" (Status)");
      echo("<br>");
    }
  }
}
unset($hosts);
unset($s);

//$javascript .= ("var MARK = [".($ii - 1)."];\n");
$ii = 0;
// put markers and bubbles onto a map
foreach ($data as $h) {
  if ($nagMapR_Debug) {
    echo('<!--'.$positionHosts.$h['host_name'].":".$h['latlng'].":".$h['status']."-->\n");
  }
    // position the host on the map
  $javascript .= ("window.".$h["host_name"]."_pos = new google.maps.LatLng(".$h["latlng"].");\n");

    // display different icons for the host (according to the status in nagios)
    // if host is in state OK
  if ($h['status'] == 0) {
    $javascript .= ("MARK.push(new google.maps.Marker({".
      "\n  position: ".$h["host_name"]."_pos,".
      "\n  icon: iconGreen,".
      "\n  map: map,".
      "\n  zIndex: 2,".
      "\n  title: \"".$h["nagios_host_name"]."\"".
      "}));"."\n\n");
    // if host is in state WARNING
  } elseif ($h['status'] == 1) {
    $javascript .= ("MARK.push(new google.maps.Marker({".
      "\n  position: ".$h["host_name"]."_pos,".
      "\n  icon: iconYellow,".
      "\n  map: map,".
      "\n  zIndex: 3,".
      "\n  title: \"".$h["nagios_host_name"]."\"".
      "}));"."\n\n");
    // if host is in state CRITICAL / UNREACHABLE
  } elseif ($h['status'] == 2) {
    $javascript .= ("MARK.push(new google.maps.Marker({".
      "\n  position: ".$h["host_name"]."_pos,".
      "\n  icon: iconRed,".
      "\n  map: map,".
      "\n  zIndex: 4,".
      "\n  title: \"".$h["nagios_host_name"]."\"".
      "}));"."\n\n");
    // if host is in state UNKNOWN
  } else {
    // if host is in any other (unknown to nagMapR) state
    $javascript .= ("window.MARK.push(new google.maps.Marker({".
      "\n  position: ".$h["host_name"]."_pos,".
      "\n  icon: 'icons/marker_grey.png',".
      "\n  map: map,".
      "\n  zIndex: 6,".
      "\n  title: \"".$h["nagios_host_name"]."\"".
      "}));"."\n\n");
  };
    //generate google maps info bubble
  if (!isset($h["parents"])) { $h["parents"] = Array(); }; 
  $info = '<div class=\"bubble\"><strong>'.$h["nagios_host_name"]."</strong><br><table>"
  .'<tr><td>'.$alias.'</td><td>:</td><td> '.$h["alias"].'</td></tr>'
  .'<tr><td>'.$hostG.'</td><td>:</td><td> '.join('<br>', $h["hostgroups"]).'</td></tr>'
  .'<tr><td>'.$addr.'</td><td>:</td><td> '.$h["address"].'</td></tr>'
  .'<tr><td>'.$other.'</td><td>:</td><td> '.join("<br>",$h['user']).'</td></tr>'
  .'<tr><td>'.$hostP.'</td><td>:</td><td> '.join('<br>' , $h["parents"]).'</td></tr>'
  .'</table>'
  .'<a href=\"/nagios/cgi-bin/statusmap.cgi\?host='.$h["nagios_host_name"].'\">Nagios map page</a>'
  .'<br><a href=\"/nagios/cgi-bin/extinfo.cgi\?type=1\&host='.$h["nagios_host_name"].'\">Nagios host page</a>';

  $javascript .= ("window.".$h["host_name"]."_mark_infowindow = new google.maps.InfoWindow({ content: '$info'})\n");

  $javascript .= ("google.maps.event.addListener(MARK[".$ii."], 'click', function() {"
    .$h["host_name"]."_mark_infowindow.open(map, MARK[".$ii."]);\n
  });\n\n");
  $ii++;
};

if($nagMapR_Lines == 1){
  $ii = 0;
// create (multiple) parent connection links between nodes/markers
  $javascript .= "// generating links between hosts\n";
  foreach ($data as $h) {
  // if we do not have any parents, just create an empty array
    if (!isset($h["latlng"]) OR (!is_array($h["parents"]))) {
      continue;
    }
    foreach ($h["parents"] as $parent) {
      if (isset($data[$parent]["latlng"])) {
      // default colors for links
        $stroke_color = "#007f00";
      // links in warning state
        if ($h['status'] == 1) { $stroke_color ='#ffff00'; }
      // links in problem state
        if ($h['status'] == 2) { $stroke_color ='#c92a2a'; }
        $javascript .= "\n";

        $linesArray .= ("LINES.push({line: null, host:\"".$h["host_name"]."\", parent:\"".$parent."\"});\n");

        $javascript .= ('LINES['.$ii.'].line = new google.maps.Polyline({'."\n".
          ' path: ['.$h["host_name"].'_pos,'.$parent.'_pos],'."\n".
          "  strokeColor: \"$stroke_color\",\n".
          "  strokeOpacity: 0.8,\n".
          "  strokeWeight: 1.5});\n");
        $javascript .= ('LINES['.$ii."].line.setMap(map);\n\n");
        $ii++;
      }
    }
  }
}

?>