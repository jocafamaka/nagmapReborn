# Nagmap Reborn Introduction

Nagmap Reborn is based on Nagmap project developed by [Marcel Hecko](https://github.com/hecko) which according to his own description is an "... super-simple application to integrate Nagios or Icinga with Google maps. The integration aims to visualize current status of network devices on aerial photography images..."

## Features

* Updating hosts status without refresh on page.
* Support for multiple languages.
* System of last occurrences (ChangesBar).
  * Content filter.
* System of sound warning.
* Notification system on the page.
* Almost total control of page characteristics.
* Powerful debug page.
* Service filter.
* Self report of errors.
* Access control.
* Support for multiple API's.

## What you can control

* Map center.
* Zoom level.
* Language.
  * Currently only available: English, Portuguese and French. (Translation contributions are welcome)
* Last occurrences view (ChangesBar).
  * ChangesBar size on screen.
  * Font size.
  * Used date format.
  * ChangesBar mode.
    * History.
    * Alert.
    * Alert lite.
  * Show content filter.
* Use system of sound warning.
* Show lines between hosts and their parents.
* Time to update hosts status.
* Icons style.
* Which API to use.
* Use or not access control.

## Compatibility

* Nagios.
* Icinga.
* Centreon.

It is possible to integrate with other systems that have the structure similar to these, if it worked with some other server monitoring system please let me know!

## Which API?
Undecided about which API to use? Here are some differences between them:

**Google Maps API**
* Requires API key to be used.
* It has limits of free use.
* It does not rely on third-party services for display or customization.
* Easier customization and use of [styles](https://github.com/jocafamaka/nagmapReborn/tree/master/styles#google-maps-api).

**Leaflet API**
* You do not need an API key to use.
* No use limits when used in conjunction with OpenStreetMap.
* Depends on third parties to display the map and use [styles](https://github.com/jocafamaka/nagmapReborn/tree/master/styles#leaflet-api).
* Most services that provide custom maps require an API key and have usage limits.

## Live preview (Google Maps)

[See here](https://jocafamaka.github.io) the Nagmap Reborn live preview:
[![Nagmap Reborn live preview](https://i.imgur.com/sTG8YBV.jpg)](https://jocafamaka.github.io)

## Hiatus notice

From 03/2019 the project will go into hiatus to add features.

This decision was taken so that all attention is focused on the development of the [v2.0.0](https://github.com/jocafamaka/nagmapReborn/tree/v2.0.0), which will bring many benefits.

### Important Notes:
* This version will continue to receive support.
* Version 1.6.4 will be the last stable version until release of v2.0.0(alpha/rc/final).
* There is no prediction yet for release of the new version.

## Support

If you experience any problems deploying Nagmap Reborn please [see here](https://github.com/jocafamaka/nagmapReborn/wiki/How-to-request-support%3F) how to request suport.

You can [see here](https://github.com/jocafamaka/nagmapReborn/wiki/) the Wiki / FAQ of Nagmap Reborn.

## Contribution

Contribution are always **welcome and recommended**! Here is how:

1. Fork the repository ([here is the guide](https://help.github.com/articles/fork-a-repo/))
1. Clone to your machine
1. Make your changes
1. Create a pull request

## Terms of use (Leaflet / OpenStreetMap / Google Maps)

* By using this API Implementation, your are agreeing to be bound by Leaflet, OpenStreetMap and Google's Terms of Use.
* This app uses the OpenStreetMap's tiles. [See here](https://wiki.osmfoundation.org/wiki/Privacy_Policy) the privacy policy.
* This app uses the Google Maps API. [See here](https://cloud.google.com/maps-platform/terms/) Google privacy policy. - [See here](https://developers.google.com/maps/documentation/javascript/usage) Google Maps usage quotas.

## Donation
As an Information Systems student about to graduate me, I use my free time to bring updates and improvements to the project, without any financial purpose.

So if you like this project, help me to continue development and free support, buy me a coffee:

[![](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=G6E995UWUM2J6&item_name=Buy+me+a+coffee&currency_code=BRL&source=url)

<p align="center"> 
    <img src="https://media.giphy.com/media/UqTEN18TcQniWLWQBM/giphy.gif">
</p>
