NagMap Reborn Introduction
=====
NagMap Reborn is an initiative to improve the original project NagMap which according to his own description is an "... super-simple application to integrate Nagios or Icinga with Google maps. The integration aims to visualize current status of network devices on aerial photography images. It also aims to give administrator basic information on devices to do further investigation of their status, such as links to MRTG and Smokeping pages of respected devices."

New Features
============
Some changes were made to the functions and logic used in the original project and from these modifications it was possible to make the fofllowing improvementes:

* Updating hosts status without refresh on page.
* Now the application supports multiple languages.
* New system of last occurrences (ChangesBar).
* New system of sound warning.
* Almost total control of page characteristics.
* Powerful debug page.<sup>(NEW)</sup>

### What you can control:
* Map center.
* Zoom level.
* Language.*
* Last occurrences view (ChangesBar).
  * ChangesBar size on screen.
  * Font size.
  * Used date format.
  * ChangesBar mode.
* Use system of sound warning.
* Show lines between hosts and their parents.
* Time to update hosts status.
* Icons style. 

*Currently only available: English and Portuguese. (Translation contribuition are welcome)

## Compatible with
* Nagios.
* Icinga.
* Centreon.

It is possible to integrate with other systems that have the structure similar to these, if it worked with some other server monitoring system please let me know!

## NagMap Reborn page

!["NagMap Reborn Page"](https://i.imgur.com/ETuH5vb.png "NagMap Reborn Page")

### Preview of animations:

![Demo CountPages alpha](https://i.imgur.com/sqwB6d8.gif)

### Contribution
Contribution are always **welcome and recommended**! Here is how:

- Fork the repository ([here is the guide](https://help.github.com/articles/fork-a-repo/)).
- Clone to your machine
- Make your changes
- Create a pull request

Terms of use (Google Maps API)
================================================================
* By using this Maps API Implementation, your are agreeing to be bound by Google's Terms of Use.
* This app uses the Maps API(s) - [See here](http://www.google.com/privacy.html) Google privacy policy.
* [See here](https://developers.google.com/maps/documentation/javascript/usage) Google Maps usage quotas.

Others
======
If you experience any problems deploying NagMap Reborn please send an email to joao_carlos.r@hotmail.com - I will do my best to assist you or to add new features into NagMap Reborn. 

### About Debug Page
The Debug page contains information that helps identify possible bugs in the application.

It is possible to know the hosts that were ignored and the reason, you can also see information about all hosts present in the Status file.

Through it, it is possible to obtain support in an easier way, in the footer of the page there is a control button of the updates of the information, as well as a button to download the information that is being displayed, when requesting support send this file in attachment to facilitate to solve problems.


You can access the Debug page through the link:
http://localhost/nagmapReborn/debugInfo **

**Make adjustments to the link if necessary.

### Remaining credits
This application was originally developed by [Marcel Hecko](https://github.com/hecko).
