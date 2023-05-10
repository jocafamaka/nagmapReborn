<?php
include_once("functions.php");

// Get list of all Nagios configuration files into an array
$files = getConfigFiles();

// List of hostgroups
$hostgroups_list = [];

// Read content of all Nagios configuration files into one huge array
foreach ($files as $file) {
	if (file_exists($file))
		$raw_data[$file] = file($file);
}

$data = filterRawData($raw_data, $files);

if (!is_array($data)) {
	$fails[] = $data;
	return false;
}

// hosts definition - we are only interested in hostname, parents and notes with position information
foreach ($data as $host) {
	if (((!empty($host["host_name"])) && (!preg_match("/^\\!/", $host['host_name'])))) {
		$hostname = safeName($host["host_name"]);
		$hosts[$hostname]['host_name'] = $hostname;
		$hosts[$hostname]['nagios_host_name'] = $host["host_name"];
		$hosts[$hostname]['alias'] = $host["alias"];

		// iterate for every option for the host
		foreach ($host as $option => $value) {
			// get parents information
			if ($option == "parents") {
				$parents = explode(',', $value);
				foreach ($parents as $parent) {
					$parent = safeName($parent);
					$hosts[$hostname]['parents'][] = $parent;
				}
				continue;
			}
			// we are only interested in latlng values from notes
			if ($option == "notes") {
				if (preg_match("/<latlng>/", $value)) {
					$value = explode("<latlng>", $value);
					$value = explode("</latlng>", $value[1]);
					$hosts[$hostname]['latlng'] = trim($value[0]);
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

					if (!in_array($hostgroup, $hostgroups_list)) {
						$hostgroups_list[] = $hostgroup;
					}
				}
			};
			// another few information we are interested in - this is a user-defined nagios variable
			if (preg_match("/^_/", trim($option))) {
				$hosts[$hostname]['user'][] = $option . ':' . $value;
			};
			unset($parent, $parents);
		}
	}
}
unset($data);

if (config('ngreborn.filter_hostgroup')) {
	foreach ($hosts as $host) {
		if (isset($hosts[$host["host_name"]]['hostgroups']) && !in_array(config('ngreborn.filter_hostgroup'), $hosts[$host["host_name"]]['hostgroups'])) {
			unset($hosts[$host["host_name"]]);
		}
	}
}

// get host statuses
$s = ngrebornStatus();

// remove hosts we are not able to render and combine those we are able to render with their statuses
foreach ($hosts as $h) {
	if ((isset($h["latlng"])) and (isset($h["host_name"])) and (isset($s[$h["nagios_host_name"]]['status']))) {
		$final_hosts[$h["host_name"]] = $h;
		$final_hosts[$h["host_name"]]['status'] = $s[$h["nagios_host_name"]]['status'];
	}
}

unset($hosts);
unset($s);
