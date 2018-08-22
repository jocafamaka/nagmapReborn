<?php 
error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);

include('../config.php');
include("../functions.php");

if(file_exists("../langs/$nagMapR_Lang.php"))
  include("../langs/$nagMapR_Lang.php");
else
  die("$nagMapR_Lang.php does not exist in the languages folder! Please set the proper \$nagMapR_Lang variable in NagMap Reborn config file!");

$version = 'v1.3.0';


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
      $reason .= "($noLatLng)";
    if(!isset($h["host_name"]))
      $reason .= " ($noHostN)";
    if(!isset($s[$h["nagios_host_name"]]['status']))
      $reason .= " ($noStatus)";
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
  <link rel="icon" href="../resources/img/NagFavIcon.ico">

  <title>NagMap Reborn <?php echo ($debugTitle." ".$version); ?></title>

  <link href="resources/css/bootstrap.min.css" rel="stylesheet">

  <link href="resources/css/style.css" rel="stylesheet">
</head>

<body>
  <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-6"><?php echo ($debugTitle. " (" .$version); ?>)  <img src="resources/img/iconQuestion.svg" class="cursor_pointer" alt="" title="<?php echo ($help); ?>" data-toggle="modal" data-target="#myModal"></img></h1>
    <p class="lead"><?php echo ($debugInfo); ?></p>
  </div>

  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><?php echo ($debugTitle. " (" .$version); ?>)</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?php echo ($debugHelp); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo ($close); ?></button>
        </div>
      </div>
    </div>
  </div>

  <div class="container" id="allInfo">
    <div id="tableh" style="display: none;"></div>
    <div id="wait"><div class="loader"></div></div>
    <div id="InContainer" class="card-deck mb-3 text-center">
    </div>
  </div>

  <div class="container">
    <footer class="pt-4 my-md-5 pt-md-5 border-top">
      <div class="row">
        <div class="col-12 col-md">
          <img class="mb-2" src="resources/img/logo.svg" alt="">
        </div>
        <div class="col-9 col-md">
          <h5>LINKS</h5>
          <ul class="list-unstyled text-small">
            <li><a class="text-muted" href="../index.php"><?php echo ($mainPage); ?></a></li>
            <li><a class="text-muted" href="https://www.github.com/jocafamaka/nagmapReborn/"><?php echo ($project); ?></a></li>
          </ul>
        </div>
        <div class="col-9 col-md">
          <p class="float-right">
            <a href="#"><?php echo ($btop); ?></a>
          </p>
        </div>
      </div>
    </footer>
  </div>

  <div id="div_fixa" title="<?php echo ($controlInfo); ?>" class="div_fixa" style="z-index:2000;" onclick="changeImg();"><img src="resources/img/loading.svg" alt="" class="cursor_pointer" id="control"></div>

  <nav class="navbar fixed-bottom navbar-expand-sm navbar-dark bg-dark">
    <a href="https://www.github.com/jocafamaka/nagmapReborn/"><img title="<?php echo ($project); ?>" class="navbar-brand" src="resources/img/logoMini.svg" alt=""></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">

      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a title="<?php echo ($appStatus); ?>" class="nav-link">Status: <span id="status"><?php echo ($starting); ?></span></a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li>
          <button id="btnDownload" title="<?php echo ($downData); ?>" class="btn btn-success navbar-btn disabled" onclick="saveTextAsFile();">Download</button>
        </li>
      </ul>
    </div>
  </nav>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="resources/js/jquery-slim.min.js"><\/script>')</script>
  <script src="resources/js/popper.min.js"></script>
  <script src="resources/js/bootstrap.min.js"></script>
  <script src="resources/js/holder.min.js"></script>
  <script>
    Holder.addTheme('thumb', {
      bg: '#55595c',
      fg: '#eceeef',
      text: 'Thumbnail'
    });

    var ignoredHosts = <?php echo json_encode($ignored); ?>;

    var divIgnored = "<h2><?php echo ($ignHosts); ?></h2><table class=\"table table-bordered\"><thead><tr><th><?php echo ($hostName); ?></th><th><?php echo ($alias); ?></th><th><?php echo ($reasons); ?></th></tr></thead><tbody>";

    for (var i = 0 ; i < ignoredHosts.length ; i++) {
      divIgnored += "<tr><td>"+ ignoredHosts[i].hostname +"</td><td>"+ ignoredHosts[i].alias +"</td><td>"+ ignoredHosts[i].reason +"</td></tr>";
    }

    divIgnored += "</tbody></table><br><h2><?php echo ($statusFile); ?></h2>";

    document.getElementById('tableh').innerHTML = divIgnored;

    function saveTextAsFile() {
      if(download){
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
    var download = false;

    function changeImg(){
      var div = document.getElementById('control');
      if(play == true) {
        document.getElementById('status').innerHTML = '<?php echo ($waiting); ?>.';
        div.src = 'resources/img/pause.svg';
        play = false;
        update = true;
      }
      else {
        div.src = 'resources/img/play.svg';
        document.getElementById('status').innerHTML = '<?php echo ($stopped); ?>.';
        play = true;
        update = false;
      }
    }

    function load(){
      document.getElementById('status').innerHTML = '<?php echo ($updating); ?>.';
      document.getElementById('control').src = 'resources/img/loading.svg';
      document.getElementById('btnDownload').classList.add('disabled');
      download = false;
      setTimeout(function(){ 
        if(update){
          document.getElementById('control').src = 'resources/img/pause.svg';
          document.getElementById('status').innerHTML = '<?php echo ($waiting); ?>.';
        }
        document.getElementById('btnDownload').classList.remove('disabled');
        download = true;
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
                newDivs = newDivs.concat("<div class=\"card card-father mb-4 border-success\"><div title=\"<?php echo ($tHost); ?>" + i +" <?php echo ($isUp); ?>\" class=\"card-header title\" style=\"background-color: #159415;\">");
              if(arrayInfo[i].status == 3)
                newDivs = newDivs.concat("<div class=\"card card-father mb-4 border-danger\"><div title=\"<?php echo ($tHost); ?>" + i +" <?php echo ($isDown); ?>\" class=\"card-header title\" style=\"background-color: #b30606;\">");
              if(arrayInfo[i].status == 4)
                newDivs = newDivs.concat("<div class=\"card card-father mb-4 border-secondary\"><div title=\"<?php echo ($tHost); ?>" + i +" <?php echo ($isunk); ?>\" class=\"card-header title\" style=\"background-color: #6c757d;\">");

              newDivs = newDivs.concat("<h4 class=\"my-0 font-weight-bold\">" + i + "</h4></div><div class=\"card-body\"><div class=\"card-deck mb-3 text-center\">");

              newDivs = newDivs.concat("<table class=\"table table-hover\"><thead><tr><th colspan =\"3\"><h1><small class=\"text-muted\">Host status</small></h1></th></tr></thead><tbody><tr><td><?php echo ($cs); ?></td><td> : </td><td>"+ arrayInfo[i]['services']['HostStatus'].hostStatus_CS +"</td></tr><tr><td><?php echo ($lhs); ?></td><td> : </td><td>"+ arrayInfo[i]['services']['HostStatus'].hostStatus_LHS +"</td></tr><tr><td><?php echo ($ltup); ?></td><td> : </td><td>"+ arrayInfo[i]['services']['HostStatus'].hostStatus_LTU +"</td></tr><tr><td><?php echo ($ltd); ?></td><td> : </td><td>"+ arrayInfo[i]['services']['HostStatus'].hostStatus_LTD +"</td></tr><tr><td><?php echo ($ltun); ?></td><td> : </td><td>"+ arrayInfo[i]['services']['HostStatus'].hostStatus_LTUNR +"</td></tr></tbody><thead><tr><th colspan =\"3\"><h1><small class=\"text-muted\">Services status</small></h1></th></tr></thead></table>");

            for(var serv in arrayInfo[i].services){
              if(serv != "HostStatus"){
                if(arrayInfo[i]['services'][serv].servStatus_CS == 0)
                  newDivs = newDivs.concat("<div class=\"card inside mb-4 border-success\"><div title=\"<?php echo ($tServ); ?>" + serv +" <?php echo ($isUp); ?>\" class=\"card-header title\" style=\"background-color: #159415;\">");
                if(arrayInfo[i]['services'][serv].servStatus_CS == 1)
                  newDivs = newDivs.concat("<div class=\"card inside mb-4 border-warning\"><div title=\"<?php echo ($tServ); ?>" + serv +" <?php echo ($inWar); ?>\" class=\"card-header title\" style=\"background-color: #c5d200;\">");
                if(arrayInfo[i]['services'][serv].servStatus_CS == 2)
                  newDivs = newDivs.concat("<div class=\"card inside mb-4 border-orange\"><div title=\"<?php echo ($tServ); ?>" + serv +" <?php echo ($inCrit); ?>\" class=\"card-header title\" style=\"background-color: #ff8d00;\">");
                if(arrayInfo[i]['services'][serv].servStatus_CS != 0 && arrayInfo[i]['services'][serv].servStatus_CS != 1 && arrayInfo[i]['services'][serv].servStatus_CS != 2)
                newDivs = newDivs.concat("<div class=\"card inside mb-4 border-secondary\"><div title=\"<?php echo ($tServ); ?>" + serv +" <?php echo ($isunk); ?>\" class=\"card-header title\" style=\"background-color: #6c757d;\">");

                newDivs = newDivs.concat("<h4 class=\"my-0 font-weight-bold\">" + serv + "</h4></div><div class=\"card-body\">");


                newDivs = newDivs.concat("<table class=\"table table-hover\"><tr><td><?php echo ($cs); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_CS +"</td></tr><tr><td><?php echo ($lhs); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LHS +"</td></tr><tr><td><?php echo ($lsc); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LSC +"</td></tr><tr><td><?php echo ($lhsc); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LHSC +"</td></tr><tr><td><?php echo ($lto); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LTO +"</td></tr><tr><td><?php echo ($ltw); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LTW +"</td></tr><tr><td><?php echo ($ltunk); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LTUNK +"</td></tr><tr><td><?php echo ($ltc); ?></td><td> : </td><td>"+ arrayInfo[i]['services'][serv].servStatus_LTC +"</td></tr></table></div></div>");
              }
            }
          newDivs = newDivs.concat("</div></div></div>");
        }

        if(document.getElementById('wait') != null){
          document.getElementById('wait').style.display = 'none';
          document.getElementById('tableh').style.display = 'block';
        }
        document.getElementById('InContainer').innerHTML = newDivs;
      }
    };
  }
}, <?php echo $nagMapR_TimeUpdate; ?>000);
</script>
<br>
</body>
</html>
