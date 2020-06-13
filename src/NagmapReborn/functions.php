<?php
function filterRawData($raw_data, $filesName)
{
    $i = 0;
    $fileNum = 0;
    $lineNum = 1;
    $in_definition = 0;
    foreach ($raw_data as $file) {
        foreach ($file as $line) {
            //remove blank spaces
            $line = trim($line);
            //remove comments from line
            $parts = explode(';', $line);
            $line = array_shift($parts);
            // if this is not an empty line or a comment...
            if ($line && !preg_match("/^;.?/", $line) && !preg_match("/^#.?/", $line)) {
                //replace many spaces with just one (or replace tab with one space)
                $line = preg_replace('/\s+/', ' ', $line);
                $line = preg_replace('/\t+/', ' ', $line);
                if ((preg_match("/define host{/", $line)) or (preg_match("/define host {/", $line)) or (preg_match("/define hostextinfo {/", $line)) or (preg_match("/define hostextinfo{/", $line)) or (preg_match("/define hostgroup {/", $line)) or (preg_match("/define hostgroup{/", $line))) {
                    //starting a new host definition
                    if (isset($in_definition) && $in_definition) {
                        return L::in_definition_error;
                    }
                    $in_definition = 1;
                    $i++;
                } elseif (preg_match("/}/", $line)) {
                    $in_definition = 0;
                } elseif ($in_definition) {
                    //split line to options and values
                    $pieces = explode(" ", $line, 2);
                    //get rid of meaningless splits
                    if (count($pieces) < 2) {
                        continue;
                    };
                    $option = trim($pieces[0]);
                    $value = trim($pieces[1]);
                    $data[$i][$option] = $value;
                }
            }
            $lineNum++;
        }
        $lineNum = 1;
        $fileNum++;
    }
    return ($data);
}

function safeName($in)
{
    $out = trim($in);
    $out = preg_replace('#[^a-z0-9A-Z]#', '_', $out);
    return $out;
}

function ngrebornStatus()
{
    $fp = fopen(config('general.status_file'), "r");
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
                $host = $value;
            }
            if (($option == "service_description")) {
                $service = $value;
            }

            if (($option == "current_state") && ($type == "servicestatus")) {

                $serviceBackup[$host] = ($value);   //Used if it is not included in the filters.

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

            if (config('ngreborn.changes_bar_mode') == 2 || config('ngreborn.changes_bar_mode') == 3) {

                if (($option == "last_time_up") && ($type == "hoststatus")) {
                    $dataTime[$host]['time_LTU'] = $value;
                }
                if (($option == "last_state_change") && ($type == "servicestatus")) {
                    $dataTime[$host]['time_LSC'] = $value;
                }
            }
        }
    }

    foreach ($data as $key => $value) {
        if (!isset($data[$key]['servStatus_CS'])) {
            $data[$key]['servStatus_CS'] = $serviceBackup[$key];
        }

        if (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 0)) {
            $data[$key]['status'] = 0;
        } elseif (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 1)) {
            $data[$key]['status'] = 1;
        } elseif (($data[$key]['hostStatus_CS'] == 0) && ($data[$key]['servStatus_CS'] == 2)) {
            $data[$key]['status'] = 2;
        } elseif ($data[$key]['hostStatus_CS'] == 1) {
            $data[$key]['status'] = 3;
        } else {
            $data[$key]['status'] = 4;
        }
    }

    unset($serviceBackup);

    if (('ngreborn.changes_bar_mode') == 2 || ('ngreborn.changes_bar_mode') == 3) {
        foreach ($data as $key => $value) {
            if ($data[$key]['status'] == 3)
                $data[$key]['time'] = $dataTime[$key]['time_LTU'];
            else
                $data[$key]['time'] = $dataTime[$key]['time_LSC'];
        }
    }

    return ($data);
}

// This is a function listing all files with Nagios configuration files into an array
// It reads nagios config file and parses out all directions for configuration directories or files
function getConfigFiles()
{
    $cfg_raw = file(config('general.cfg_file'));

    foreach ($cfg_raw as $line) {
        $line = trim($line);
        if (preg_match("/^cfg_file/i", $line)) {
            $file = explode('=', $line, 2);
            $file[1] = trim($file[1]);
            $files[] = $file[1];
            unset($file);
        } elseif (preg_match("/^cfg_dir/i", $line)) {
            $dir = explode('=', $line, 2);
            $dir[1] = trim($dir[1]);
            readRecursiveDir($files, $dir[1]);
        }
    }
    $file_list = array_unique($files);
    return $file_list;
}

//Function to read recursively a config directory which contains symlinks
function readRecursiveDir(&$files, $dir)
{
    $dir_recursive = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($dir_recursive as $file => $object) {
        if (preg_match("/.cfg$/i", $file)) {
            $files[] = $file;
        } elseif (is_link($file) || (is_dir($file) && !preg_match("/\.$/i", $file))) {
            readRecursiveDir($files, $file);
        }
    }
}
