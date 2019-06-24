<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

$key = $_POST['key'];
$lastFile = $_POST['lastFile'];

include_once('config.php');
include_once("langs/$nagMapR_Lang.php");

function randomId($length = 10) {
	return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function writeCacheFile($hStatus, $fileCache = "", $flNamePart = ""){

	foreach($hStatus as $key => $value){
		$currentStatus[$key]['status'] = $value['status'];
	}

	if(empty($fileCache)){
		$flName = randomId() . '-' . time();
		$fileCache = @fopen('cache/.ht'.$flName.'.json', 'w+');
	}
	else{
		$flName =  $flNamePart . '-' . time();
	}

	$write = @fwrite($fileCache, json_encode($currentStatus));

	if(!($fileCache && $write)){

		if($fileCache)
			@fclose($fileCache);

		return false;
	}

	@fclose($fileCache);

	return $flName;
}

if($key == $nagMapR_key){

	header('Content-Type: application/json');

	if (!file_exists($nagios_status_dat_file)) {
		die("$nagios_status_dat_file $file_not_find_error");
	}

	$fp = @fopen($nagios_status_dat_file,"r");
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
			unset($service);
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
			if (($option == "service_description")) {
				$service = $value;
			}

			if (($option == "current_state") && ($type == "servicestatus")) {
                //Used if it is not included in the filters.
				$serviceBackup[$host] = ($value);

				if($nagMapR_FilterService != ''){

					$servicesFilter = explode(';', $nagMapR_FilterService);

					foreach ($servicesFilter as $serviceFilter) {
						if (strpos(strtoupper($service), strtoupper($serviceFilter)) !== false){
							$data[$host]['servStatus_CS'] = ($value);
						}
					}
				}
				else{
					$data[$host]['servStatus_CS'] = ($value);
				}
			}
			if (($option == "current_state") && ($type == "hoststatus")) {

				$data[$host]['hostStatus_CS'] = ($value);
			}

			if($nagMapR_ChangesBarMode == 2 || $nagMapR_ChangesBarMode == 3) {

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
						$data[$host]['time_LTU'] = ( $minutes. " min");
					else{
						$data[$host]['time_LTU'] = ( $hours. " h ". $and ." " .$minutes. " min");
					}
				}
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
						$data[$host]['time_LSC'] = ( $minutes. " min");
					else{
						$data[$host]['time_LSC'] = ( $hours. " h ". $and ." " .$minutes. " min");
					}
				}
			}
		}
	}

	foreach ($data as $key => $value) {
		if( !isset($data[$key]['servStatus_CS']) ){
			$data[$key]['servStatus_CS'] = $serviceBackup[$key];
		}

		if (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 0)) {
			$hStatus[$key]['status'] = 0;

		} elseif (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 1)) {
			$hStatus[$key]['status'] = 1;

		} elseif (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 2)) {
			$hStatus[$key]['status'] = 2;

		} elseif ($data[$key]['hostStatus_CS'] == 1)  {
			$hStatus[$key]['status'] = 3;
		}
		else{
			$hStatus[$key]['status'] = 4;
		}
	}

	unset($serviceBackup);

	foreach ($hStatus as $key => $value) {
		if($hStatus[$key]['status'] == 3)
			$hStatus[$key]['time'] = $data[$key]['time_LTU'];
		else
			$hStatus[$key]['time'] = $data[$key]['time_LSC'];
	}


	if(!empty($lastFile)){

		$response['leak'] = false;

		$fileInCache = @fopen('cache/.ht'.$lastFile.'.json', 'r+');

		$read = @fread($fileInCache, 10000);

		@fseek($fileInCache, 0);

		if($fileInCache && $read){

			$oldHostsStatus = json_decode($read, true);

      		//print_r($oldHostsStatus);

			foreach($oldHostsStatus as $key => $oldStatus){
				/* echo ($key . ' st: '. $oldStatus['status'] . ' | (' . $hStatus[$key] . ') st: ' . $hStatus[$key]['status']); */
				try{
					if($oldStatus['status'] != $hStatus[$key]['status']){
						$updateHosts[$key]['status'] = $hStatus[$key]['status'];
						$updateHosts[$key]['time'] = $hStatus[$key]['time'];
					}
				}
				catch(Exception $e){
					$response['leak'] = true;
				}
			}

			if(isset($updateHosts))
				$response['hosts'] = $updateHosts;
			else
				$response['hosts'] = "{}";

			$nameParts = explode("-", $lastFile);

			if(! ($flNewName = writeCacheFile($hStatus, $fileInCache, $nameParts[0])))
				$flNewName = "";

			if(! rename('cache/.ht'.$lastFile.'.json', 'cache/.ht'.$flNewName.'.json'))
				$flNewName = "";

			$response['lastFile'] = $flNewName;
		}
		else{
			if($fileInCache)
				@fclose($fileInCache);

			$response['hosts'] = $hStatus;

			if(! ($flName = writeCacheFile($hStatus)))
				$flName = "";

			$response['lastFile'] = $flName;
		}

	}
	else{
		$response['hosts'] = $hStatus;

		if(! ($flName = writeCacheFile($hStatus)))
			$flName = "";

		$response['lastFile'] = $flName;
	}

	echo json_encode($response);
}
