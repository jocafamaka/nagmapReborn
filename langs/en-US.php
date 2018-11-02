<?php 	// File that defines language variables.
/*
 * ##################################################################
 * #             ALL CREDITS FOR MODIFICATIONS ARE HERE             #
 * ##################################################################
 *
 * KEEP THE PATTERN
 *
 * Original Credits: João Ribeiro (https://github.com/jocafamaka) in 04 March 2018
 *
 */

//Errors:
$var_cfg_error = ("has not been properly configured, check the NagMap Reborn configuration file and make the necessary corrections! Defined value: ");

$moduleError = ("A PHP module/extension essential for running NagMap Reborn was not found, please install the module/extension before proceeding. Module/Extension name: ");

$file_not_find_error = ("does not exist! Please set the variable in NagMap Reborn config file!\n");

$in_definition_error =("Starting a new in_definition before closing the previous one! That is not cool.");

$no_data_error = ("There is no data to display. You either did not set NagMap Reborn properly or there is a software bug.<br>Please contact joao_carlos.r@hotmail.com assistance.");

$reported = (" reported.");

$errorFound = ("An error was automatically reported.");

$reporterErrorPre =("An error has occurred but could not be reported!");

$reporterError =("This version of NagMap Reborn is no longer supported for bug fixes. Please use the <a href='https://github.com/jocafamaka/nagmapReborn/releases'>latest available version</a>.");

$emptyUserPass = ("Authentication username and/or password have not been defined, set user and password in the configuration file.");

$updateError = ("There was a problem updating the status of hosts, displayed statuses may be outdated, check the console for more information.");

$updateErrorServ = ("This type of error is usually related to the following problem: The server is inaccessible or recusing connections, check the server and your connection.");

$updateErrorStatus = ("This type of error is usually related to the following problem: The status file is inaccessible or does not exist, verify that the monitoring service is executing correctly.");

$updateErrorChanges = ("This type of error is usually related to the following problem: Host modifications, addition, removal or editing of names have occurred, in this case update the page.");

$updateErrorSolved = ("Issue solved, the statuses displayed are in real time.");

//Debug info:
$message = ("Message:");

$lineNum = ("Line number:");

$error = ("Error");

$at = ("At:");

//Bubble info:
$alias = ("Alias");

$hostG = ("Hostgroups");

$addr = ("Address");

$other = ("Other");

$hostP = ("Parents");

$newVersion = ("Update available");

$newVersionText = ("<br>The currently used version of NagMap Reborn is outdated!<br><br>Get the new version on GitHub:<br><br>");

$passAlertTitle = ("Default authentication");

$passAlert = ("Currently you are using the password and default user, do not be unprotected, modify it now!");

//ChangesBar warnings:
$up = ("UP");

$down = ("DOWN");

$warning = ("WARNING");

$unknown = ("UNKNOWN");

$critical = ("CRITICAL");

$and = ("and");

$waiting = ("Waiting");

$timePrefix = ('');

$timeSuffix = (' ago.');

$filter = ("Filter");

//Debug page
$debugTitle = ("Debug Info.");

$debugInfo = ("This page contains important information that can help in case of bugs. Among this informations are the hosts ignored with the reason, and additional information about each of the hosts present in the status file.");

$updating = ("Updating");

$mainPage = ("Main page");

$project = ("Project on GitHub");

$btop = ("Back to top");

$starting = ("Starting, wait.");

$stopped = ("Stopped");

$downData = ("Download data");

$ignHosts = ("Ignored hosts (static)");

$statusFile = ("Status file info (dynamic)");

$hostName = ("Host name");

$reasons = ("Reason(s)");

$tServ = ("The service ");

$tHost = ("The host ");

$cs = ("Current state");

$lhs = ("Last hard state");

$lsc = ("Last state change");

$lhsc = ("Last hard state change");

$ltup = ("Last time up");

$ltd = ("Last time down");

$ltun = ("Last time unreachable");

$lto = ("Last time ok");

$ltw = ("Last time warning");

$ltunk = ("Last time unknown");

$ltc = ("Last time critical");

$isUp = ("is up");

$isDown = ("is down");

$inWar = ("is in warning");

$incrit = ("is critical");

$isunk = ("has an unknown status");

$controlInfo = ("Stop/Start information update");

$appStatus = ("Current application status");

$noLatLng = ("It has no definition of LatLng in the settings");

$noHostN = ("Do not have a HostName");

$noStatus = ("It does not exist in the Status file");

$help = ("Help");

$close = ("Close");

$primary = (" (Primary)");

$debugHelp = ('This page contains helpful information when requesting support!<br><br>

The characteristics of the pages are these:<br><br>

<strong>1 - Hosts that were ignored.</strong><br>
     - Displays all the ignored hosts.<br>
     - Informs the host name.<br>
     - The host alias.<br>
     - The reasons or motives of that host have been ignored.<br>
     - The reasons can be very useful to define if it was a configuration error or application bug.<br><br>

<strong>2 - Important information about each host in the Status file.</strong><br>
     - The color of the Card indicates the status of the host or service in question.<br>
     - Shows information about internal status.<br>
         - Green: ok; Yellow: warning; Orange: critical; Gray: unknown.<br>
     - Displays the time values for several parameters.<br>
     - Displays the time in Epoch format and the time in hours and minutes.<br><br>

<strong>3 - In the footer of the page there is the controller to update the page information.</strong><br>
     - It is possible to stop the update at any time, useful for capturing quick events.<br>
     - There is also a download button that downloads a file with the information on the page right now.<br>
     - The download button is disabled during page information updates.<br>
<br>
<strong>Whenever requesting support</strong> access the debug page download the file and attach in your request, this procedure can and will make troubleshooting easier.<br><br>

You can get support by contacting me via e-mail: <strong>joao_carlos.r@hotmail.com</strong>');

//Auth

$authFail = ("Authentication failed! Try again.");
?>