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

function filter_raw_data($raw_data, $filesName) {
  include('config.php');
  include("langs/$nagMapR_Lang.php");

  $i=0; $fileNum=0; $lineNum=1;
  foreach ($raw_data as $file) {
    foreach ($file as $line) {
      //remove blank spaces
      $line = trim($line);
      //remove comments from line
      $line = array_shift(explode(';',$line));
      // if this is not an empty line or a comment...
      if ($line && !preg_match("/^;.?/", $line) && !preg_match("/^#.?/", $line)) {
        //replace many spaces with just one (or replace tab with one space)
        $line = preg_replace('/\s+/', ' ', $line);
        $line = preg_replace('/\t+/', ' ', $line);
        if (
          (preg_match("/define host{/", $line)) OR
          (preg_match("/define host {/", $line)) OR
          (preg_match("/define hostextinfo {/", $line)) OR
          (preg_match("/define hostextinfo{/", $line)) OR
          (preg_match("/define hostgroup {/", $line)) OR
          (preg_match("/define hostgroup{/", $line))
        ) {
          //starting a new host definition
          if ($in_definition) {
            die($in_definition_error);
          }
          $in_definition = 1;
          $i++;
        } elseif (preg_match("/}/",$line)) {
          $in_definition = 0;
        } elseif ($in_definition) {
          //split line to options and values
          $pieces = explode(" ", $line, 2);
          //get rid of meaningless splits
          if (count($pieces)<2) {
            die($one_column_error1.$line.$one_column_error2.$filesName[$fileNum]." (".$lineNum.")");
          };
          $option = trim($pieces[0]);
          $value = trim($pieces[1]);
          $data[$i][$option] = $value;
        }
      }
      $lineNum++;
    }
    $lineNum=1;
    $fileNum++;
  }
  return($data);
}

function safe_name($in) {
  $out = trim($in);
  $out = mb_convert_encoding($out, "ASCII");
  $out = str_replace('-','_',$out);
  $out = str_replace('.','_',$out);
  $out = str_replace('/','_',$out);
  $out = str_replace('(','_',$out);
  $out = str_replace(')','_',$out);
  $out = str_replace(' ','_',$out);
  $out = str_replace(',','_',$out);
  $out = str_replace("'",'',$out);
  return $out;
}

function nagMapR_status() {
  include('config.php');
  include("langs/$nagMapR_Lang.php");

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
      //get the worst service state for the host from all of its services
      if (!isset($data[$host]['servicestatus']['current_state'])) {
        $data[$host]['servicestatus']['current_state'] = "0";
      }
      if ($option == "current_state") {
        if ($value >= $data[$host][$type][$option]) {
          $data[$host][$type][$option] = $value;
        }
        if (($data[$host]['hoststatus']['current_state'] == 0) && ($data[$host]['servicestatus']['current_state'] == 0)) {
          $data[$host]['status'] = 0;
          $data[$host]['status_human'] = 'OK';
          $data[$host]['status_style'] = 'ok';
        } elseif (($data[$host]['hoststatus']['current_state'] == 1)) {
          $data[$host]['status'] = 2;
          $data[$host]['status_human'] = 'CRITICAL / DOWN';
          $data[$host]['status_style'] = 'critical';
        } 
        else 
        {
          $data[$host]['status'] = 1;
          $data[$host]['status_human'] = 'WARNING / UNREACHABLE';
          $data[$host]['status_style'] = 'warning';          
        }
      } 
    }
  }
  return $data;
}

// This is a function listing all files with Nagios configuration files into an array
// It reads nagios config file and parses out all directions for configuration directories or files
function get_config_files() {
  include('config.php');
  include("langs/$nagMapR_Lang.php");

  if (!file_exists($nagios_cfg_file)){
    die("$nagios_cfg_file $file_not_find_error");
  }

  $cfg_raw = file($nagios_cfg_file);

  $comment = ";";
  $comment2 = "#";
  foreach ($cfg_raw as $line) {
    $line = trim($line);
    if (preg_match("/^cfg_file/i",$line)) {
      $file = explode('=',$line,2);
      $file[1] = trim($file[1]);
      $files[] = $file[1];
      //echo "\n\n// including Nagios config file ".$file[1].", config reference $line\n";
      unset($file);
    } elseif (preg_match("/^cfg_dir/i",$line)) {
      $dir = explode('=',$line,2);
      $dir[1] = trim($dir[1]);
      read_recursive_dir($files, $dir[1]);
    }
  }
  //echo "\n\n// end of reading config file $nagios_cfg_file\n\n";
  $file_list = array_unique($files);
  return $file_list;
}

//Function to read recursively a config directory which contains symlinks
function read_recursive_dir(&$files, $dir){
  $dir_recursive = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
  foreach ($dir_recursive as $file => $object) {
    if(preg_match("/.cfg$/i",$file)) {
      $files[] = $file;
      //echo "\n\n// including Nagios config file ".$file.", config reference ".$line."\n";
    } elseif (is_link($file) || (is_dir($file) && !preg_match("/\.$/i",$file)) ) {
      read_recursive_dir($files, $file);
    }
  }
}

?>
