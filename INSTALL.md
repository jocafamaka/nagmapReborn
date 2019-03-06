# Installation

## Configuration

The 'notes' field for host definition needs to match the following example:
```
<latlng>66.174082,-13.119136</latlng>
```
If you are editing configuration files by hand this is how the FULL notes
line should look like:
```
  notes       <latlng>66.174082,-13.119136</latlng>
```
If you use the notes field to display other information, you can make this definition as a line comment:
```
  notes    OTHER STUFF   #<latlng>66.174082,-13.119136</latlng>
```

You can get the precise coordinates from [OSM](https://www.openstreetmap.org/).
Right click on the spot where your device is located and select option "Show Addres".
The coordinates will be set in maps search bar.

To draw connections, hosts need to have 'parents' field set up.

## Apache / Nagmap Reborn setup

Your webserver (e.g. Apache) needs to have PHP support enabled!
Please make sure your PHP is version 7.0 and above :) (see phpinfo() php function)

On some distributions you might need to install mbstring for PHP - like Fedora/RedHat:
yum install php-mbstring

1) copy the Nagmap Reborn directory into your Apache web folder
   (/var/www/ on Debian/Ubuntu, or /var/www/html/ on RedHat/CentOS)

2) copy config.php.example into config.php and edit this file (config.php)
   to fit your needs

3) Open the website of Nagmap Reborn installation
   (e.g. open Firefox - point to YOURIP/nagmapReborn)

## Dependencies
The only dependencies of NagMap Reborn are **PHP extensions/modules**, make sure they are installed:

* json.
* mbstring.

### JS Compability
Since v2.0.0-alpha1 of the Nagmap Reborn, features of ES6 (ECMAScript 2015) are used, so it is only possible to use the project in more modern browsers.

The following is the minimum supported version relationship:
| Browser | Min. version |
| -- | :--: |
| Chrome | 58 |
| Firefox | 54 |
| Edge | 14	|
| Safari | 10 |
| Opera | 55 |
| Internet Explorer | Does not support |
_* You can see the full list [here](http://kangax.github.io/compat-table/es6/)._

For compatibility with legacy browsers, use an earlier version [(1.6.3)](https://github.com/jocafamaka/nagmapReborn/releases/tag/v1.6.3) of the project.

## Support

joao_carlos.r@hotmail.com - Please do not hesitate to send an email!
I will eventualy respond to everyone!