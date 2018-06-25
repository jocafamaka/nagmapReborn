# NagMap Reborn Introduction

NagMap Reborn is an initiative to improve the original project NagMap which according to his own description is an "... super-simple application to integrate Nagios or Icinga with Google maps. The integration aims to visualize current status of network devices on aerial photography images. It also aims to give administrator basic information on devices to do further investigation of their status, such as links to MRTG and Smokeping pages of respected devices."

## New Features

Some changes were made to the functions and logic used in the original project and from these modifications it was possible to make the following improvementes:

* Updating hosts status without refresh on page.
* Now the application supports multiple languages.
* New system of last occurrences (ChangesBar).
* New system of sound warning.
* Almost total control of page characteristics.
* Powerful debug page.

## What you can control

* Map center.
* Custom map style. <sup>(NEW)</sup>
* Zoom level.
* Language.
  * Currently only available: English and Portuguese. (Translation contributions are welcome)
* Last occurrences view (ChangesBar).
  * ChangesBar size on screen.
  * Font size.
  * Used date format.
  * ChangesBar mode.
* Use system of sound warning.
* Show lines between hosts and their parents.
* Time to update hosts status.
* Icons style.

## Compatible with

* Nagios.
* Icinga.
* Centreon.

It is possible to integrate with other systems that have the structure similar to these, if it worked with some other server monitoring system please let me know!

## NagMap Reborn page

!["NagMap Reborn Page"](https://i.imgur.com/4rg98IC.png "NagMap Reborn Page")

<sup>Using the Custom Dark Style</sup>

## Preview of animations

![Demo CountPages alpha](https://i.imgur.com/hLzJ6T6.gif "NagMap Reborn Animation")

<sup>Using the Custom Dark Style</sup>

## Contribution

Contribution are always **welcome and recommended**! Here is how:

1. Fork the repository ([here is the guide](https://help.github.com/articles/fork-a-repo/))
1. Clone to your machine
1. Make your changes
1. Create a pull request

## Terms of use (Google Maps API)

* By using this Maps API Implementation, your are agreeing to be bound by Google's Terms of Use.
* This app uses the Maps API(s) - [See here](http://www.google.com/privacy.html) Google privacy policy.
* [See here](https://developers.google.com/maps/documentation/javascript/usage) Google Maps usage quotas.

## Others

If you experience any problems deploying NagMap Reborn please send an email to joao_carlos.r@hotmail.com - I will do my best to assist you or to add new features into NagMap Reborn.

## About private API Key

Since June 11, 2018, the use of the API private key has been mandatory even in local networks, so a specific field has been added for this purpose inside the configuration file.

[See here](https://developers.google.com/maps/documentation/javascript/get-api-key) how to get an API private key.

## About Debug Page

The Debug page contains information that helps identify possible bugs in the application.

It is possible to know the hosts that were ignored and the reason, you can also see information about all hosts present in the Status file.

Though it is also possible to obtain support in an easier way - in the footer of the page there is a control button for the information about updates, as well as a button to download the information that is being displayed. When requesting support send this file in attachment to facilitate to solve problems.

You can access the Debug page through the link:
[http://localhost/nagmapReborn/debugInfo](http://localhost/nagmapReborn/debugInfo)<sup>1</sup>

1: Make adjustments to the link if necessary.

## Remaining credits

This application was originally developed by [Marcel Hecko](https://github.com/hecko).
