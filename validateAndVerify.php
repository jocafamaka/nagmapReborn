<?php
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
  die("\$nagios_cfg_file $var_cfg_error ($nagios_cfg_file)");

if(!is_string($nagios_status_dat_file)) 
  die("\$nagios_status_dat_file $var_cfg_error ($nagios_status_dat_file)");

if(($nagMapR_MapAPI < 0) || ($nagMapR_MapAPI > 1))
  die("\$nagMapR_MapAPI $var_cfg_error ($nagMapR_MapAPI)");

if($nagMapR_MapAPI == 0){

  if(!is_string($nagMapR_Mapkey) || empty($nagMapR_Mapkey)) 
    die("\$nagMapR_Mapkey $var_cfg_error ($nagMapR_Mapkey)");

  if(!is_string($nagMapR_MapType)) 
    die("\$nagMapR_MapType $var_cfg_error ($nagMapR_MapType)");

  if($nagMapR_Style != ''){
    if(!file_exists("styles/$nagMapR_Style.json")){
      die("\$nagMapR_Style $var_cfg_error ($nagMapR_Style)");
    }
  }
}

if(!is_string($nagMapR_FilterHostgroup)) 
  die("\$nagMapR_FilterHostgroup $var_cfg_error ($nagMapR_FilterHostgroup)");

if(!is_string($nagMapR_MapCentre)) 
  die("\$nagMapR_MapCentre $var_cfg_error ($nagMapR_MapCentre)");

if(!is_string($nagMapR_key)) 
  die("\$nagMapR_key $var_cfg_error ($nagMapR_key)");

if(!is_int($nagMapR_Debug))
  die("\$nagMapR_Debug $var_cfg_error ($nagMapR_Debug)");

if(!is_int($nagMapR_DateFormat))
  die("\$nagMapR_DateFormat $var_cfg_error ($nagMapR_DateFormat)");

if(!is_int($nagMapR_PlaySound))
  die("\$nagMapR_PlaySound $var_cfg_error ($nagMapR_PlaySound)");

if(!is_int($nagMapR_ChangesBar))
  die("\$nagMapR_ChangesBar $var_cfg_error ($nagMapR_ChangesBar)");

if(($nagMapR_ChangesBarMode < 1) || ($nagMapR_ChangesBarMode > 2))
  die("\$nagMapR_ChangesBarMode $var_cfg_error ($nagMapR_ChangesBarMode)");

if(($nagMapR_Reporting < 0) || ($nagMapR_Reporting > 1))
  die("\$nagMapR_Reporting $var_cfg_error ($nagMapR_Reporting)");

if(!is_int($nagMapR_Lines))
  die("\$nagMapR_Lines $var_cfg_error ($nagMapR_Lines)");

if(!( is_float($nagMapR_ChangesBarSize) || is_int($nagMapR_ChangesBarSize) ))
  die("\$nagMapR_ChangesBarSize $var_cfg_error ($nagMapR_ChangesBarSize)");

if(!( is_float($nagMapR_FontSize) || is_int($nagMapR_FontSize) ))
  die("\$nagMapR_FontSize $var_cfg_error ($nagMapR_FontSize)");

if(!( is_float($nagMapR_MapZoom) || is_int($nagMapR_MapZoom) ))
  die("\$nagMapR_MapZoom $var_cfg_error ($nagMapR_MapZoom)");

if(!( is_float($nagMapR_TimeUpdate) || is_int($nagMapR_TimeUpdate) ))
  die("\$nagMapR_TimeUpdate $var_cfg_error ($nagMapR_TimeUpdate)");

if($nagMapR_IconStyle > 2 || $nagMapR_IconStyle < 0)
  die("\$nagMapR_IconStyle $var_cfg_error ($nagMapR_IconStyle)");

if(!extension_loaded('mbstring'))
  die("$moduleError mbstring");

if(!extension_loaded('json'))
  die("$moduleError json");

function checkUserPass(){
  include('config.php');

  if($nagMapR_User == "ngradmin" && $nagMapR_UserKey == "ngradmin")
    return true;
  else
    return false;

}
?>