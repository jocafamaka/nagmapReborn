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

$file_not_find_error = ("does not exist! Please set the variable in NagMap Reborn config file!\n");

$in_definition_error =("Starting a new in_definition before closing the previous one! That is not cool.");

$one_column_error1 = ("In-hose config file line (");

$one_column_error2 = (") which contains only one column. This is not right! File and line:");

$no_data_error = ("There is no data to display. You either did not set NagMap Reborn properly or there is a software bug.<br>Please contact joao_carlos.r@hotmail.com assistance.");

//Debug info:
$ignoredHosts = ("Ignoring the following host: ");

$positionHosts = ("Positioning host: ");

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

$newVersion = ("New version available");

$newVersionText = ("The version of NagMap Reborn that you are currently using is not updated.<br><br>Download the new version to have access to news and improvements.<br><br>Find the new version on GitHub:<br><br>");

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

$isunk = ("unknown");

$controlInfo = ("Stop/Start information update");

$appStatus = ("Current application status");

$noLatLng = ("It has no definition of LatLng in the settings");

$noHostN = ("Do not have a HostName");

$noStatus = ("It does not exist in the Status file");

$help = ("Help");

$close = ("Close");

$debugHelp = ('This page contains helpful information when requesting support!<br><br>

The characteristics of the pages are these:<br><br>

1 - Hosts that were ignored.<br>
     - Displays all the ignored hosts.<br>
     - Informs the host name.<br>
     - The host alias.<br>
     - The reasons or motives of that host have been ignored.<br>
     - The reasons can be very useful to define if it was a configuration error or application bug.<br><br>

2 - Important information about each host in the Status file.<br>
     - The color of the Card indicates the status of the host in question.<br>
     - Shows information about internal stats.<br>
     - Displays the time values for several parameters.<br>
     - Displays the time in Epoch format and the time in hours and minutes.<br><br>

3 - In the footer of the page there is the controller to update the page information.<br>
     - It is possible to stop the update at any time, useful for capturing quick events.<br>
     - There is also a download button that downloads a file with the information on the page right now.<br>
     - The download button is disabled during page information updates.<br>

Whenever requesting support access the debug page download the file and attach in your request, this procedure can and will make troubleshooting easier.<br><br>

You can get support by contacting me via e-mail: joao_carlos.r@hotmail.com');
?>