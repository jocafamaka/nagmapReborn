<?php

error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);

$key = $_GET['key'];
$search = $_GET['s'];

include_once('../config.php');
include_once('../functions.php');
include_once("../langs/$nagMapR_Lang.php");
include_once('../marker.php');

//Require auth
require_auth();

if($key != $nagMapR_key)
	die("<html><head></head><body style='color:white;background:black;'>".$authFail."</body><script>setTimeout(()=>{location.reload()},".$nagMapR_TimeUpdate."000)</script></html>");

if($nagMapR_ChangesBar != 1 || $nagMapR_ChangesBarMode != 3)
	die("<html><head></head><body style='color:white;background:black;'>Not activated.</body><script>setTimeout(()=>{location.reload()},".$nagMapR_TimeUpdate."000)</script></html>");

function formatTime($value){
	include('../config.php');
	include("../langs/$nagMapR_Lang.php");

	if($value > 0){
		$pastTime = time() - $value;
		$hours = floor($pastTime / 3600);
		$minutes = intval(($pastTime / 60) % 60);
	}
	else{
		$hours = 0;
		$minutes = 0;
	}

	if($hours == 0)
		return ($minutes. " min");
	else{						
		return ($hours. " h ". $and ." " .$minutes. " min");
	}
}

$war = "";
$cri = "";
$down = "";

foreach ($jsData as $key => $value) {
	if($jsData[$key]['status'] == 1){
		$war .= ('<div class="changesBarLine WAR" style="font-size: '. $nagMapR_FontSize .'px; opacity: 0;">'.$jsData[$key]['alias'].' - '. $timePrefix . formatTime($jsData[$key]['time']) . $timeSuffix .'</div>');
	}

	if($jsData[$key]['status'] == 2){
		$cri .= ('<div class="changesBarLine CRIT" style="font-size: '. $nagMapR_FontSize .'px; opacity: 0;">'.$jsData[$key]['alias'].' - '. $timePrefix . formatTime($jsData[$key]['time']) . $timeSuffix .'</div>');
	}

	if($jsData[$key]['status'] == 3){
		$down .= ('<div class="changesBarLine DOWN" style="font-size: '. $nagMapR_FontSize .'px; opacity: 0;">'.$jsData[$key]['alias'].' - '. $timePrefix . formatTime($jsData[$key]['time']) . $timeSuffix .'</div>');
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Changes Bar Lite</title>
	<link rel="shortcut icon" href="img/NagFavIcon.ico" />
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link rel=StyleSheet href="style.css" type="text/css" media=screen>
	<style type="text/css">html, body{margin: 0;padding: 0;width: 100%;height: 100%;background: black;} #changesbar{height: 100%!important;}.form-group .cleanS{min-width: calc(10% - 19px) !important;}.form-group input{width: calc(100% - 10%);}.changesBarLine{cursor: context-menu !important; display: block;}.form-group{position: fixed; top: 0;left: 0;width: 100%;}</style>
</head>
<body>

	<?php
	if($nagMapR_BarFilter == 1){
		echo '<div class="form-group"><input style="font-size:'.$nagMapR_FontSize.'px;" value="'.$search.'" type="text" id="searchBar" class="form-control" placeholder="'.$filter.'..."><dic class="cleanS" onclick="$(\'#searchBar\').val(\'\');search();" style="font-size:'.$nagMapR_FontSize.'px;" title="'.$clear.'"><span>'.$clear.'</span></div></div>';

		echo '<div id="changesbar" style="padding-top:'.(($nagMapR_FontSize) * 2 - 7).'px; padding-left: 1px; background: black; overflow:none;">';
	}
	else{
		echo '<div id="changesbar" style="padding-top:2px; padding-left: 1px; background: black; overflow:none;">';
	}

	echo('<div id="downHosts">'.$down.'</div>');

	echo('<div id="critHosts">'.$cri.'</div>');

	echo('<div id="warHosts">'.$war.'</div>');

	echo('</div>');

	echo('<div id="error" style="background:#fff;font-size:'.$nagMapR_FontSize.'px; display:none; width:100%; height:100%;">'.$updateError.'</div>');

	?>
	<script src="../debugInfo/resources/js/jquery.min.js"></script>
	<script type="text/javascript">

		function error(){
			if($('.form-group'))
				$('.form-group').hide();

			$('#changesbar').hide();

			$('#error').show();
		}
		
		try{
			<?php
			if($nagMapR_BarFilter == 1)
				echo ("
					$('#searchBar').keyup(function(){
						search();
					});

					function search(){
						var query = \$('#searchBar').val().toLowerCase();
						\$('#changesbar .changesBarLine').each(function(){
							var \$this = \$(this);
							if(\$this.text().toLowerCase().indexOf(query) === -1)
								\$this.closest('#changesbar .changesBarLine').hide();
							else 
								\$this.closest('#changesbar .changesBarLine').show();
						});
					};

					setTimeout(function(){
						window.location.replace('changesBarLite.php?key=".$nagMapR_key."&s='+$('#searchBar').val().toLowerCase());
					}, ".$nagMapR_TimeUpdate."000);

					search();
					");
			else
				echo ("
					setTimeout(function(){
						window.location.replace('changesBarLite.php?key=".$nagMapR_key."');
					}, ".$nagMapR_TimeUpdate."000)
					");
			?>
		}
		catch(err){
			console.log(err.message);
			error();
		}

		setTimeout(function(){
			$('#changesbar .changesBarLine').each(function(){
				$(this).css('opacity', '1');
			});
		}, 10);
	</script>
</body>
</html>