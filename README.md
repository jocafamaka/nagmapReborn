# NagMap Reborn Introduction

NagMap Reborn is an initiative to improve the NagMap project developed by [Marcel Hecko](https://github.com/hecko) which according to his own description is an "... super-simple application to integrate Nagios or Icinga with Google maps. The integration aims to visualize current status of network devices on aerial photography images..."

## New Features

Some changes were made to the functions and logic used in the original project and from these modifications it was possible to make the following improvementes:

* Updating hosts status without refresh on page.
* Support for multiple languages.
* System of last occurrences (ChangesBar).
* System of sound warning.
* Notification system on the page.
* Almost total control of page characteristics.
* Powerful debug page.
* Service filter.
* Self report of errors.
* Access control. <sup>(NEW)</sup>

## What you can control

* Map center.
* Custom map style.
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

## Live preview

[See here](https://jocafamaka.github.io) the NagMap Reborn live preview:
[![NagMap Reborn live preview](https://i.imgur.com/Mc26Pn5.png)](https://jocafamaka.github.io)

## NagMap Reborn with Leaflet

Is a modified version of NagMap Reborn, which uses the [Leaflet library](https://leafletjs.com/) for map rendering instead of the Google Maps API.

**ATTENTION:** This version is still under development and being tested and should **not be used in production!**

See the repository [here](https://github.com/jocafamaka/nagmapReborn-leaflet).

## Support

If you experience any problems deploying NagMap Reborn please [see here](https://github.com/jocafamaka/nagmapReborn/wiki/How-to-request-support%3F) how to request suport.

You can [see here](https://github.com/jocafamaka/nagmapReborn/wiki/) the Wiki / FAQ of NagMap Reborn.

## Contribution

Contribution are always **welcome and recommended**! Here is how:

1. Fork the repository ([here is the guide](https://help.github.com/articles/fork-a-repo/))
1. Clone to your machine
1. Make your changes
1. Create a pull request

## Terms of use (Google Maps API)

* By using this Maps API Implementation, your are agreeing to be bound by Google's Terms of Use.
* This app uses the Maps API(s) - [See here](https://cloud.google.com/maps-platform/terms/) Google privacy policy.
* [See here](https://developers.google.com/maps/documentation/javascript/usage) Google Maps usage quotas.