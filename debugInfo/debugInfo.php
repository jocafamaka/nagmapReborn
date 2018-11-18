<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$key = $_POST['key'];

include('../config.php');
include("../langs/$nagMapR_Lang.php");

if($key == $nagMapR_key){
	if (!file_exists($nagios_status_dat_file)) {
		die("</script>$nagios_status_dat_file $file_not_find_error");
	}
	$fp = fopen($nagios_status_dat_file,"r");
	$type = "";
	$hStatus = Array();
	while (!feof($fp)) {
		$line = trim(fgets($fp));
		//ignore all commented lines - hop to the next itteration
		if (empty($line) OR preg_match("/^;/", $line) OR preg_match("/^#/", $line)) {
			continue;
		}
		//if end of definition, skip to next itteration
		if (preg_match("/}/",$line)) {
			$type = "0";
			unset($host);
			unset($service);
			continue;
		}
		if (preg_match("/^hoststatus {/", $line)) {
			$type = "hoststatus";
		};
		if (preg_match("/^servicestatus {/", $line)) {
			$type = "servicestatus";
		};
		if(!preg_match("/}/",$line) && ($type == "hoststatus" || $type == "servicestatus")) {
			$line = trim($line);
			$pieces = explode("=", $line, 2);
			//do not bother with invalid data
			if (count($pieces)<2) { continue; };
			$option = trim($pieces[0]);
			$value = trim($pieces[1]);
			if (($option == "host_name")) {
				$host = $value;
			}
			if (($option == "service_description")) {
				$service = $value;
				if($nagMapR_FilterService != ''){

					$servicesFilter = explode(';', $nagMapR_FilterService);

					foreach ($servicesFilter as $serviceFilter) {
						if (strpos(strtoupper($service), strtoupper($serviceFilter)) !== false){
							$service .= $primary;
						}
					}
				}
			}
			if (($option == "last_state_change") && ($type == "hoststatus")) {

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
					$hStatus[$host]['services']['HostStatus']['hostStatus_LSC'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services']['HostStatus']['hostStatus_LSC'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_hard_state_change") && ($type == "hoststatus")) {

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
					$hStatus[$host]['services']['HostStatus']['hostStatus_LHSC'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services']['HostStatus']['hostStatus_LHSC'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_up") && ($type == "hoststatus")) {

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
					$hStatus[$host]['services']['HostStatus']['hostStatus_LTU'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services']['HostStatus']['hostStatus_LTU'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_down") && ($type == "hoststatus")) {

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
					$hStatus[$host]['services']['HostStatus']['hostStatus_LTD'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services']['HostStatus']['hostStatus_LTD'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_unreachable") && ($type == "hoststatus")) {

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
					$hStatus[$host]['services']['HostStatus']['hostStatus_LTUNR'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services']['HostStatus']['hostStatus_LTUNR'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "current_state") && ($type == "hoststatus")) {
				
				$hStatus[$host]['services']['HostStatus']['hostStatus_CS'] = ($value);
			}
			if (($option == "last_hard_state") && ($type == "hoststatus")) {
				
				$hStatus[$host]['services']['HostStatus']['hostStatus_LHS'] = ($value);
			}
			####################################################################################################
			if (($option == "last_state_change") && ($type == "servicestatus")) {

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
					$hStatus[$host]['services'][$service]['servStatus_LSC'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services'][$service]['servStatus_LSC'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_hard_state_change") && ($type == "servicestatus")) {

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
					$hStatus[$host]['services'][$service]['servStatus_LHSC'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services'][$service]['servStatus_LHSC'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_ok") && ($type == "servicestatus")) {

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
					$hStatus[$host]['services'][$service]['servStatus_LTO'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services'][$service]['servStatus_LTO'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_warning") && ($type == "servicestatus")) {

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
					$hStatus[$host]['services'][$service]['servStatus_LTW'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services'][$service]['servStatus_LTW'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_unknown") && ($type == "servicestatus")) {

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
					$hStatus[$host]['services'][$service]['servStatus_LTUNK'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services'][$service]['servStatus_LTUNK'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "last_time_critical") && ($type == "servicestatus")) {

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
					$hStatus[$host]['services'][$service]['servStatus_LTC'] = ("(". $value .") → " .$minutes. " min");
				else{						
					$hStatus[$host]['services'][$service]['servStatus_LTC'] = ("(". $value .") → " .$hours. " h ". $and ." " .$minutes. " min");
				}
			}
			if (($option == "current_state") && ($type == "servicestatus")) {
				
				$hStatus[$host]['services'][$service]['servStatus_CS'] = ($value);
			}
			if (($option == "last_hard_state") && ($type == "servicestatus")) {
				
				$hStatus[$host]['services'][$service]['servStatus_LHS'] = ($value);
			}
			if(isset($hStatus[$host]['services']['HostStatus']['hostStatus_CS']))
			{
				if (($hStatus[$host]['services']['HostStatus']['hostStatus_CS'] == 0) ) {
					$hStatus[$host]['status'] = 0;

				} elseif ($hStatus[$host]['services']['HostStatus']['hostStatus_CS'] == 1)  {
					$hStatus[$host]['status'] = 3;
				}
				else{
					$hStatus[$host]['status'] = 4;
				}
			}
		}
	}
	echo json_encode($hStatus);
}
?>