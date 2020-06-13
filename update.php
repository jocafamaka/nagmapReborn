<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (!defined('NGR_DOCUMENT_ROOT')) {
	define('NGR_DOCUMENT_ROOT', dirname(__FILE__) == '/' ? '' : dirname(__FILE__));
}

include_once(NGR_DOCUMENT_ROOT . '/src/NagmapReborn/ConfigLoader.php');
include_once(NGR_DOCUMENT_ROOT . '/src/NagmapReborn/Helper.php');

require_once(NGR_DOCUMENT_ROOT . "/src/NagmapReborn/i18n.class.php");
$i18n = new i18n(NGR_DOCUMENT_ROOT . "/langs/" . config('ngreborn.language') . ".json", NGR_DOCUMENT_ROOT . "/cache/");
$i18n->init();

requiredAuth(config('security.use_auth'), config('security.user'), config('security.user_pass'), L::class);

$key = @$_POST['key'];
$currentHosts = @$_POST['hosts'];

function safeName($in)
{
	$out = trim($in);
	$out = preg_replace('#[^a-z0-9A-Z]#', '_', $out);
	return $out;
}

if ($key == config('security.key')) {

	$fp = @fopen(config('general.status_file'), "r");
	$type = "";
	$data = array();
	while (!feof($fp)) {
		$line = trim(fgets($fp));
		//ignore all commented lines - hop to the next itteration
		if (empty($line) or preg_match("/^;/", $line) or preg_match("/^#/", $line)) {
			continue;
		}
		//if end of definition, skip to next itteration
		if (preg_match("/}/", $line)) {
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
		if (!preg_match("/}/", $line) && ($type == "hoststatus" | $type == "servicestatus")) {
			$line = trim($line);
			$pieces = explode("=", $line, 2);
			//do not bother with invalid data
			if (count($pieces) < 2) {
				continue;
			};
			$option = trim($pieces[0]);
			$value = trim($pieces[1]);
			if (($option == "host_name")) {
				$host = safeName($value);
			}
			if (($option == "service_description")) {
				$service = $value;
			}

			if (($option == "current_state") && ($type == "servicestatus")) {
				//Used if it is not included in the filters.
				$serviceBackup[$host] = ($value);

				if (config('ngreborn.filter_service') != '') {

					$servicesFilter = explode(';', config('ngreborn.filter_service'));

					foreach ($servicesFilter as $serviceFilter) {
						if (strpos(strtoupper($service), strtoupper($serviceFilter)) !== false) {
							$data[$host]['servStatus_CS'] = ($value);
						}
					}
				} else {
					$data[$host]['servStatus_CS'] = ($value);
				}
			}
			if (($option == "current_state") && ($type == "hoststatus")) {

				$data[$host]['hostStatus_CS'] = ($value);
			}

			if (config('ngreborn.changes_bar.mode') == 2 || config('ngreborn.changes_bar.mode') == 3) {

				if (($option == "last_time_up") && ($type == "hoststatus")) {

					if ($value > 0) {
						$pastTime = time() - $value;
						$hours = floor($pastTime / 3600);
						$minutes = intval(($pastTime / 60) % 60);
					} else {
						$hours = 0;
						$minutes = 0;
					}

					if ($hours == 0)
						$data[$host]['time_LTU'] = ($minutes . " min");
					else {
						$data[$host]['time_LTU'] = ($hours . " h " . L::and . " " . $minutes . " min");
					}
				}
				if (($option == "last_state_change") && ($type == "servicestatus")) {

					if ($value > 0) {
						$pastTime = time() - $value;
						$hours = floor($pastTime / 3600);
						$minutes = intval(($pastTime / 60) % 60);
					} else {
						$hours = 0;
						$minutes = 0;
					}

					if ($hours == 0)
						$data[$host]['time_LSC'] = ($minutes . " min");
					else {
						$data[$host]['time_LSC'] = ($hours . " h " . L::and . " " . $minutes . " min");
					}
				}
			}
		}
	}

	foreach ($data as $key => $value) {
		if (!isset($data[$key]['servStatus_CS'])) {
			$data[$key]['servStatus_CS'] = $serviceBackup[$key];
		}

		if (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 0)) {
			$hStatus[$key]['status'] = 0;
		} elseif (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 1)) {
			$hStatus[$key]['status'] = 1;
		} elseif (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 2)) {
			$hStatus[$key]['status'] = 2;
		} elseif ($data[$key]['hostStatus_CS'] == 1) {
			$hStatus[$key]['status'] = 3;
		} else {
			$hStatus[$key]['status'] = 4;
		}
	}

	unset($serviceBackup);

	if (config('ngreborn.changes_bar.mode') == 2 || config('ngreborn.changes_bar.mode') == 3) {

		foreach ($hStatus as $key => $value) {
			if ($hStatus[$key]['status'] == 3)
				$hStatus[$key]['time'] = $data[$key]['time_LTU'];
			else
				$hStatus[$key]['time'] = $data[$key]['time_LSC'];
		}
	}

	$currentHosts = json_decode($currentHosts, true);

	$updateHosts = [];
	$response['missing'] = false;

	foreach ($currentHosts as $key => $hostName) {
		try {
			if (array_key_exists($hostName[0], $hStatus)) {
				$updateHosts[$hostName[0]]['status'] = $hStatus[$hostName[0]]['status'];
				if (config('ngreborn.changes_bar.mode') == 2 || config('ngreborn.changes_bar.mode') == 3)
					$updateHosts[$hostName[0]]['time'] = $hStatus[$hostName[0]]['time'];
			} else {
				$response['missing'] = true;
			}
		} catch (Exception $e) {
			$response['missing'] = true;
		}
	}

	$response['hosts'] = $updateHosts;

	return jsonResponse($response);
} else {
	return jsonResponse(['error' => L::accessDenied], 422);
}
