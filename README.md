# NagMap Reborn Introduction

NagMap Reborn is based on NagMap project developed by [Marcel Hecko](https://github.com/hecko) which according to his own description is an "... super-simple application to integrate Nagios or Icinga with Google maps. The integration aims to visualize current status of network devices on aerial photography images..."

## Features

* Updating hosts status without refresh on page.
* Support for multiple languages.
* System of last occurrences (ChangesBar).
  * Content filter. <sup>(NEW)</sup>
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
  * Show content filter. <sup>(NEW)</sup>
* Use system of sound warning.
* Show lines between hosts and their parents.
* Time to update hosts status.
* Icons style.
* Which API to use. 

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

[See here](https://jocafamaka.github.io) the NagMap Reborn live preview:
[![NagMap Reborn live preview](https://i.imgur.com/Mc26Pn5.png)](https://jocafamaka.github.io)

## Under development

The master branch will always contain the code referring to the latest stable version.

See [here](https://github.com/jocafamaka/nagmapReborn/tree/developing) the version in development, this version contains the latest modifications and new features.

It should be noted that this version may contain features that are still being tested and should **NOT** be used in production under any circumstances!

## Support

If you experience any problems deploying NagMap Reborn please [see here](https://github.com/jocafamaka/nagmapReborn/wiki/How-to-request-support%3F) how to request suport.

You can [see here](https://github.com/jocafamaka/nagmapReborn/wiki/) the Wiki / FAQ of NagMap Reborn.

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
