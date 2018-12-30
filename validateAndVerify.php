<?php
$fails = '';

// Check if the config file exist.
if(file_exists("config.php"))
  include('config.php');
else
  die("The 'config.php' file was not found in the project folder. Please check the existence of the file and if the name is correct and try again.");

// Check if the translation file informed exist.
if(file_exists("langs/$nagMapR_Lang.php"))
  include("langs/$nagMapR_Lang.php");
else
  die("$nagMapR_Lang.php does not exist in the languages folder! Please set the proper \$nagMapR_Lang variable in NagMap Reborn config file!");

// Validation of configuration variables.
if(!is_string($nagios_cfg_file)) 
  $fails = "<b>\$nagios_cfg_file</b> $var_cfg_error ($nagios_cfg_file)";

if(!is_string($nagios_status_dat_file)) 
  $fails .= "<br><b>\$nagios_status_dat_file</b> $var_cfg_error ($nagios_status_dat_file)";

if(($nagMapR_MapAPI < 0) || ($nagMapR_MapAPI > 1) || (!isset($nagMapR_MapAPI)))
  $fails .= "<br><b>\$nagMapR_MapAPI</b> $var_cfg_error ($nagMapR_MapAPI)";

if($nagMapR_MapAPI == 0){

  if(!is_string($nagMapR_Mapkey) || !isset($nagMapR_Mapkey)) 
    $fails .= "<br><b>\$nagMapR_Mapkey</b> $var_cfg_error ($nagMapR_Mapkey)";

  if(!is_string($nagMapR_MapType)) 
    $fails .= "<br><b>\$nagMapR_MapType</b> $var_cfg_error ($nagMapR_MapType)";

  if($nagMapR_Style != ''){
    if(!file_exists("styles/$nagMapR_Style.json")){
      $fails .= "<br><b>\$nagMapR_Style</b> $var_cfg_error ($nagMapR_Style)";
    }
  }
}

if(!is_string($nagMapR_FilterHostgroup)) 
  $fails .= "<br><b>\$nagMapR_FilterHostgroup</b> $var_cfg_error ($nagMapR_FilterHostgroup)";

if(!is_string($nagMapR_MapCentre)) 
  $fails .= "<br><b>\$nagMapR_MapCentre</b> $var_cfg_error ($nagMapR_MapCentre)";

if((!is_string($nagMapR_key)) || !isset($nagMapR_key)) 
  $fails .= "<br><b>\$nagMapR_key</b> $var_cfg_error ($nagMapR_key)";

if(!is_int($nagMapR_Debug))
  $fails .= "<br><b>\$nagMapR_Debug</b> $var_cfg_error ($nagMapR_Debug)";

if(!is_int($nagMapR_DateFormat))
  $fails .= "<br><b>\$nagMapR_DateFormat</b> $var_cfg_error ($nagMapR_DateFormat)";

if(!is_int($nagMapR_PlaySound))
  $fails .= "<br><b>\$nagMapR_PlaySound</b> $var_cfg_error ($nagMapR_PlaySound)";

if(!is_int($nagMapR_ChangesBar))
  $fails .= "<br><b>\$nagMapR_ChangesBar</b> $var_cfg_error ($nagMapR_ChangesBar)";

if(!is_int($nagMapR_BarFilter))
  $fails .= "<br><b>\$nagMapR_BarFilter</b> $var_cfg_error ($nagMapR_BarFilter)";

if(($nagMapR_ChangesBarMode < 1) || ($nagMapR_ChangesBarMode > 3))
  $fails .= "<br><b>\$nagMapR_ChangesBarMode</b> $var_cfg_error ($nagMapR_ChangesBarMode)";

if(($nagMapR_Reporting < 0) || ($nagMapR_Reporting > 1)  || (!isset($nagMapR_Reporting)))
  $fails .= "<br><b>\$nagMapR_Reporting</b> $var_cfg_error ($nagMapR_Reporting)";

if(!is_int($nagMapR_Lines))
  $fails .= "<br><b>\$nagMapR_Lines</b> $var_cfg_error ($nagMapR_Lines)";

if(!( is_float($nagMapR_ChangesBarSize) || is_int($nagMapR_ChangesBarSize) ))
  $fails .= "<br><b>\$nagMapR_ChangesBarSize</b> $var_cfg_error ($nagMapR_ChangesBarSize)";

if(!( is_float($nagMapR_FontSize) || is_int($nagMapR_FontSize) ))
  $fails .= "<br><b>\$nagMapR_FontSize</b> $var_cfg_error ($nagMapR_FontSize)";

if(!( is_float($nagMapR_MapZoom) || is_int($nagMapR_MapZoom) ))
  $fails .= "<br><b>\$nagMapR_MapZoom</b> $var_cfg_error ($nagMapR_MapZoom)";

if(!( is_float($nagMapR_TimeUpdate) || is_int($nagMapR_TimeUpdate) ))
  $fails .= "<br><b>\$nagMapR_TimeUpdate</b> $var_cfg_error ($nagMapR_TimeUpdate)";

if($nagMapR_IconStyle > 2 || $nagMapR_IconStyle < 0  || (!isset($nagMapR_IconStyle)))
  $fails .= "<br><b>\$nagMapR_IconStyle</b> $var_cfg_error ($nagMapR_IconStyle)";

if(($nagMapR_useAuth < 0) || ($nagMapR_useAuth > 1)  || (!isset($nagMapR_useAuth))){
  $fails .= "<br><b>\$nagMapR_useAuth</b> $var_cfg_error ($nagMapR_useAuth)";
}
else{
  if($nagMapR_useAuth == 1){
    if(empty($nagMapR_User) || empty($nagMapR_UserKey))
      $fails .= "<br>$emptyUserPass";
  }
}

if(!extension_loaded('mbstring'))
  $fails .= "<br>$moduleError mbstring";

if(!extension_loaded('json'))
  $fails .= "<br>$moduleError json";

if(!empty($fails))
  die("<h1>Nagmap Reborn ". file_get_contents('VERSION') ."</h1><hr>".$fails);

function checkUserPass(){
  include('config.php');
  if($nagMapR_useAuth == 1){

    if($nagMapR_User == "ngradmin" && $nagMapR_UserKey == "ngradmin")
      return true;
    else
      return false;
  }
  return false;
}

//Function to generate hash of files avoiding problems with encode.
function fileHash($file){
  $data = file_get_contents($file);
  $arr = explode(PHP_EOL, $data); 
  return md5(serialize($arr));
}

$checkFile = parse_ini_file("resources/checkFiles.ini");

$nagMapR_OriginalFiles = "true";

foreach ($checkFile as $key => $value) {
  if(fileHash($key) != $value){
    $nagMapR_OriginalFiles = "false";
    break;
  }
}
?>