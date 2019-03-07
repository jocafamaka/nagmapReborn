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

$reporterErrorOF =("Could not report bug because one or more major project files have been modified!");

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

$newVersionFooter = ("It will close after 10 seconds.");

$newVersionText = ("<br>The new Nagmap Reborn is now available.<br><br>Get it now to enjoy all the news and improvements!<br>");

$getNow = ("Get it now!");

$passAlertTitle = ("Default authentication");

$passAlert = ("Currently you are using the password and default user, do not be unprotected, modify it now!");

$asFilter = ("Use as a filter");

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

$clear = ("Clear");

//Debug page
$debugOff = ("The debug is disabled, to access this page activate the debug in the configuration file.");

$debugTitle = ("Debug Info.");

$updating = ("Updating");

$mainPage = ("Main page");

$project = ("Project on GitHub");

$btop = ("Back to top");

$starting = ("Starting, wait.");

$stopped = ("Stopped");

$downData = ("Download data");

$verifications = ("Checks (static)");

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

$outFilterHg = ("It is not in the filtered HostGroup.");

$help = ("Help");

$close = ("Close");

$primary = (" (Primary)");

$debugHelp = ('This page contains helpful information when requesting support!

The characteristics of the pages are these:

<strong>1 - Checks.</strong>
     - Displays information about access control.
     - Displays information about automatic error reporting.
     - Displays information about file integrity.
     - Reports important data and warnings about reported data.

<strong>2 - Hosts that were ignored.</strong>
     - Displays all the ignored hosts.
     - Informs the host name.
     - The host alias.
     - The reasons or motives of that host have been ignored.
     - The reasons can be very useful to define if it was a configuration error or application bug.

<strong>3 - Important information about each host in the Status file.</strong>
     - The color of the Card indicates the status of the host or service in question.
     - Shows information about internal status.
         - Green: ok; Yellow: warning; Orange: critical; Gray: unknown.
     - Displays the time values for several parameters.
     - Displays the time in Epoch format and the time in hours and minutes.

<strong>4 - In the footer of the page there is the controller to update the page information.</strong>
     - It is possible to stop the update at any time, useful for capturing quick events.
     - There is also a download button that downloads a file with the information on the page right now.
     - The download button is disabled during page information updates.

<strong>Whenever requesting support</strong> access the debug page download the file and attach in your request, this procedure can and will make troubleshooting easier.

You can get support by contacting me via e-mail: <strong>joao_carlos.r@hotmail.com</strong>');

//Auth

$authFail = ("Authentication failed! Try again.");

$noAuthDanger = ("<strong>Danger: </strong> Access control is currently disabled!");

$defaultPassUser = ("<strong>Attention: </strong> ".$passAlert);

$AuthOk = ("<strong>All right: </strong> Access control is enabled and default user change and password has been performed.");

$reportOffOF = ("<strong>Danger: </strong> One or more main project files have been modified, so it is not possible to automatically report bugs.");

$reportOff = ("<strong>Attention: </strong> Automatic errors reporting is disabled!");

$reportOk = ("<strong>All right: </strong> All errors are reported automatically!");

$reportDataRequestP1 = ('*To request all data collected from error reports, please send an email to: joao_carlos.r@hotmail.com
<br>In the subject: Data of the report of errors.
<br>In the body of the email, enter the domain (url) through which the service is accessed and your');

$reportDataRequestP2 = ('report token');

$reportDataRequestP3 = ('<br>Your request will be dealt with as soon as possible, if necessary for security reasons, other forms of domain verification will be requested.');

$yourRToken = ("Your report token");

$accessControl = ("Access control");

$errorReporting = ("Error reporting");

$fileIntegrity = ("Files integrity");

$reportCountP1 = ("<strong>Data: </strong>You have made");

$reportCountP2 = ("valid report(s) in the last 7 days.");

$debugFile = ("File");

$debugIntegrity = ("Integrity");
?>