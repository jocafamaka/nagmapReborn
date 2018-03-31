<?php 
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);

include('../config.php');
include("../functions.php");


$files = get_config_files();

foreach ($files as $file) {
  $raw_data[$file] = file($file);
}

$data = filter_raw_data($raw_data, $files);

foreach ($data as $host) {
  if (((!empty($host["host_name"])) && (!preg_match("/^\\!/", $host['host_name']))) | ($host['register'] == 0)) {
    $hostname = 'x'.safe_name($host["host_name"]).'x';
    $hosts[$hostname]['host_name'] = $hostname;
    $hosts[$hostname]['nagios_host_name'] = $host["host_name"];
    $hosts[$hostname]['alias'] = $host["alias"];

    foreach ($host as $option => $value) {
      if ($option == "parents") {
        $parents = explode(',', $value); 
        foreach ($parents as $parent) {
          $parent = safe_name($parent);
          $hosts[$hostname]['parents'][] = "x".$parent."x";
        }
        continue;
      }
      if ($option == "notes") {
        if (preg_match("/latlng/",$value)) { 
          $value = explode(":",$value); 
          $hosts[$hostname]['latlng'] = trim($value[1]);
          continue;
        } else {
          continue;
        }
      };
      if (($option == "address")) {
        $hosts[$hostname]['address'] = trim($value);
      };
      if (($option == "hostgroups")) {
        $hostgroups = explode(',', $value);
        foreach ($hostgroups as $hostgroup) {
          $hosts[$hostname]['hostgroups'][] = $hostgroup;
        }
      };
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

$s = nagMapR_status();

$ii = 0;

foreach ($hosts as $h) {
  if ((!isset($h["latlng"])) || (!isset($h["host_name"])) || (!isset($s[$h["nagios_host_name"]]['status']))) { 
    $ignored[$ii]['hostname'] = $h['host_name'];
    $ignored[$ii]['alias'] = $h['alias'];

    if(!isset($h["latlng"]))
      $reason .= "(It has no definition of LatLng in the settings)";
    if(!isset($h["host_name"]))
      $reason .= " (Do not have a HostName)";
    if(!isset($s[$h["nagios_host_name"]]['status']))
      $reason .= " (It does not exist in the Status file)";
    $ignored[$ii]['reason'] = $reason;
    $reason = "";
    $ii++;
  }
}
unset($hosts);
unset($s);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../icons/NagFavIcon.ico">

  <title>NagMap Reborn Debug Inormation</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">

  <link href="css/style.css" rel="stylesheet">
</head>

<body>
  <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-6">Debug Info (v1.0.0)</h1>
    <p class="lead">This page contains important information that can help in case of bugs. Among this informations are the hosts ignored with the reason, and additional information about each of the hosts present in the status file.</p>
  </div>

  <div class="container" id="allInfo">
    <div id="tableh" style="display: none;"></div>
    <div id="wait"><div class="loader"></div></div>
    <div id="InContainer" class="card-deck mb-3 text-center">
    </div>

    <footer class="pt-4 my-md-5 pt-md-5 border-top">
      <div class="row">
        <div class="col-12 col-md">
          <img class="mb-2" src="img/logo.svg" alt="">
        </div>
        <div class="col-9 col-md">
          <h5>LINKS</h5>
          <ul class="list-unstyled text-small">
            <li><a class="text-muted" href="../index.php">Main page</a></li>
            <li><a class="text-muted" href="https://www.github.com/jocafamaka/nagmapReborn/">Project on GitHub</a></li>
          </ul>
        </div>
        <div class="col-9 col-md">
          <p class="float-right">
            <a href="#">Back to top</a>
          </p>
        </div>
      </div>
    </footer>
  </div>

  <div id="div_fixa" title="Stop/Start information update" class="div_fixa" style="z-index:2000;" onclick="changeImg();"><img src="img/loading.svg" alt="" id="control"></div>

  <nav class="navbar fixed-bottom navbar-expand-sm navbar-dark bg-dark">
    <a href="https://www.github.com/jocafamaka/nagmapReborn/"><img title="Go to NagMap Reborn page on GitHub" class="navbar-brand" src="img/logoMini.svg" alt=""></img></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">

      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a title="Current application status" class="nav-link">Status: <span id="status">Starting, wait.</span></a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li>
          <button id="btnDownload" title="Download data" class="btn btn-success navbar-btn disabled" onclick="saveTextAsFile();">Download</button>
        </li>
      </ul>
    </div>
  </nav>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/jquery-slim.min.js"><\/script>')</script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/holder.min.js"></script>
  <script>
    Holder.addTheme('thumb', {
      bg: '#55595c',
      fg: '#eceeef',
      text: 'Thumbnail'
    });

    var ignoredHosts = <?php echo json_encode($ignored); ?>;

    var divIgnored = "<h2>Ignored hosts (static)</h2><table class=\"table table-bordered\"><thead><tr><th>Host Name</th><th>Alias</th><th>Reason(s)</th></tr></thead><tbody>";

    for (var i = 0 ; i < ignoredHosts.length ; i++) {
      divIgnored += "<tr><td>"+ ignoredHosts[i].hostname +"</td><td>"+ ignoredHosts[i].alias +"</td><td>"+ ignoredHosts[i].reason +"</td></tr>";
    }

    divIgnored += "</tbody></table><br><h2>Status file info (dynamic)</h2>";

    document.getElementById('tableh').innerHTML = divIgnored;

    function saveTextAsFile() {
      if(down){
        var textToWrite = document.getElementById("allInfo").innerHTML;
        var textFileAsBlob = new Blob([textToWrite], {
          type: 'text/plain'
        });

        var downloadLink = document.createElement("a");
        downloadLink.download = "DebugInfo";
        downloadLink.innerHTML = "Download File";
        if (window.URL != null) {

          downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
        } else {

          downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
          downloadLink.onclick = destroyClickedElement;
          downloadLink.style.display = "none";
          document.body.appendChild(downloadLink);
        }

        downloadLink.click();
      }
    }

    function destroyClickedElement(event) {
      document.body.removeChild(event.target);
    }


    var play = false;
    var update = true;
    var down = false;

    function changeImg(){
      var div = document.getElementById('control');
      if(play == true) {
        document.getElementById('status').innerHTML = 'Waiting.';
        div.src = 'img/pause.svg';
        play = false;
        update = true;
      }
      else {
        div.src = 'img/play.svg';
        document.getElementById('status').innerHTML = 'Stopped.';
        play = true;
        update = false;
      }
    }

    function load(){
      document.getElementById('status').innerHTML = 'Updating.';
      document.getElementById('control').src = 'img/loading.svg';
      document.getElementById('btnDownload').classList.add('disabled');
      down = false;
      setTimeout(function(){ 
        if(update){
          document.getElementById('control').src = 'img/pause.svg';
          document.getElementById('status').innerHTML = 'Waiting.';
        }
        document.getElementById('btnDownload').classList.remove('disabled');
        down = true;
      }, 2500);
    };

    var newDivs = "";

    setInterval(function(){
      if(update){

        load();

        var ajax = new XMLHttpRequest();

        var arrayHosts;

        ajax.open('POST', 'debugInfo.php?key=<?php echo $nagMapR_key ?>', true);

        ajax.send();

        ajax.onreadystatechange = function(){

          if(ajax.readyState == 4 && ajax.status == 200) {
            arrayInfo = JSON.parse(ajax.responseText);

            var hosts = [];

            newDivs = "";

            for(var i in arrayInfo){
              if(arrayInfo[i].status == 0)
                newDivs = newDivs.concat("<div class=\"card mb-4 border-success\"><div title=\"Host "+ i +" is up\" class=\"card-header\" style=\"background-color: #159415;");
              if(arrayInfo[i].status == 1)
                newDivs = newDivs.concat("<div class=\"card mb-4 border-warning\"><div title=\"Host "+ i +" is in warning\" class=\"card-header\" style=\"background-color: #c5d200;");
              if(arrayInfo[i].status == 2)
                newDivs = newDivs.concat("<div class=\"card mb-4 border-danger\"><div title=\"Host "+ i +" is down\" class=\"card-header\" style=\"background-color: #b30606;");

              newDivs = newDivs.concat("color: white; text-shadow:2px 2px 4px #000000 ;\"><h4 class=\"my-0 font-weight-bold\">" + i + "</h4></div><div class=\"card-body\"><ul class=\"list-unstyled mt-3 mb-4\"><h1><small class=\"text-muted\">HostStatus</small></h1><table><tr><td>Current state</td><td> : </td><td>"+ arrayInfo[i].hostStatus_CS +"</td></tr><tr><td>Last hard state</td><td> : </td><td>"+ arrayInfo[i].hostStatus_LHS +"</td></tr><tr><td>Last state change</td><td> : </td><td>"+ arrayInfo[i].hostStatus_LSC +"</td></tr><tr><td>Last hard state change</td><td> : </td><td>"+ arrayInfo[i].hostStatus_LHSC +"</td></tr><tr><td>Last time up</td><td> : </td><td>"+ arrayInfo[i].hostStatus_LTU +"</td></tr><tr><td>Last time down</td><td> : </td><td>"+ arrayInfo[i].hostStatus_LTD +"</td></tr><tr><td>Last time unreachable</td><td> : </td><td>"+ arrayInfo[i].hostStatus_LTUNR +"</td></tr></table><h1><small class=\"text-muted\">ServiceStatus</small></h1><table><tr><td>Current state</td><td> : </td><td>"+ arrayInfo[i].servStatus_CS +"</td></tr><tr><td>Last hard state</td><td> : </td><td>"+ arrayInfo[i].servStatus_LHS +"</td></tr><tr><td>Last state change</td><td> : </td><td>"+ arrayInfo[i].servStatus_LSC +"</td></tr><tr><td>Last hard state change</td><td> : </td><td>"+ arrayInfo[i].servStatus_LHSC +"</td></tr><tr><td>Last time ok</td><td> : </td><td>"+ arrayInfo[i].servStatus_LTO +"</td></tr><tr><td>Last time warning</td><td> : </td><td>"+ arrayInfo[i].servStatus_LTW +"</td></tr><tr><td>Last time unknown</td><td> : </td><td>"+ arrayInfo[i].servStatus_LTUNK +"</td></tr><tr><td>Last time critical</td><td> : </td><td>"+ arrayInfo[i].servStatus_LTC +"</td></tr></table></ul></div></div>");
            }
            if(document.getElementById('wait') != null){
              document.getElementById('wait').style.display = 'none';
              document.getElementById('tableh').style.display = 'block';
            }
            document.getElementById('InContainer').innerHTML = newDivs;
          }
        };
      }
    }, 10000);
  </script>
  <br>
</body>
</html>
