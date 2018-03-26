<?php
/*
 * ##################################################################
 * #             ALL CREDITS FOR MODIFICATIONS ARE HERE             #
 * ##################################################################
 *
 * KEEP THE PATTERN
 *
 * Original Credits: João Ribeiro (https://github.com/jocafamaka) in 06 March 2018
 *
 */
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$key = $_GET['key'];

include('config.php');
include("langs/$nagMapR_Lang.php");

if($key == $nagMapR_key){
	if (!file_exists($nagios_status_dat_file)) {
		die("</script>$nagios_status_dat_file $file_not_find_error");
	}
	$fp = fopen($nagios_status_dat_file,"r");
	$type = "";
	$data = Array();
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
			continue;
		}
		if (preg_match("/^hoststatus {/", $line)) {
			$type = "hoststatus";
		};
		if (preg_match("/^servicestatus {/", $line)) {
			$type = "servicestatus";
		};
		if(!preg_match("/}/",$line) && ($type == "hoststatus" | $type == "servicestatus")) {
			$line = trim($line);
			$pieces = explode("=", $line, 2);
			//do not bother with invalid data
			if (count($pieces)<2) { continue; };
			$option = trim($pieces[0]);
			$value = trim($pieces[1]);
			if (($option == "host_name")) {
				$host = $value;
			}
			if($nagMapR_ChangesBarMode == 2){
				if (($option == "last_state_change")) {

					$pastTime = time() - $value;
					$hours = floor($pastTime / 3600);
					$minutes = intval(($pastTime / 60) % 60);

					if($hours == 0)
						$hStatus[$host]['last_state_change'] = ($minutes. " min");
					else{						
						$hStatus[$host]['last_state_change'] = ($hours. " h ". $and ." " .$minutes. " min");
					}
				}
			}
			//get the worst service state for the host from all of its services
			if (!isset($data[$host]['servicestatus']['current_state'])) {
				$data[$host]['servicestatus']['current_state'] = "0";
			}
			if ($option == "current_state") {
				if ($value >= $data[$host][$type][$option]) {
					$data[$host][$type][$option] = $value;
				}
				if (($data[$host]['hoststatus']['current_state'] == 0) && ($data[$host]['servicestatus']['current_state'] == 0)) {
					$hStatus[$host]['status'] = 0;

				} elseif (($data[$host]['hoststatus']['current_state'] == 1)) {
					$hStatus[$host]['status'] = 2;
				} 
				else 
				{
					$hStatus[$host]['status'] = 1;         
				}
			} 
		}
	}
	echo json_encode($hStatus);
}

?>