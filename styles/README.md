NagMap Reborn Styles
=====
You can customize the map as you wish, define in the configuration file the variable:
```PHP
$nagMapR_Style or $nagMapR_StyleLeaflet
```

## Leaflet API
You can see several styles for free [here](http://leaflet-extras.github.io/leaflet-providers/preview/).
Some of these need a token to access, paste the full link.

For example: 
```PHP
$nagMapR_StyleLeaflet = 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_nolabels/{z}/{x}/{y}{r}.png';
```

How to use the Mapbox styles [here](https://github.com/jocafamaka/nagmapReborn/wiki/How-to-use-the-Mapbox-styles%3F).

## Google Maps API
With the name of the chosen style, set the variable in the configuration file:
```PHP
$nagMapR_Style = 'Style_Chosen';
```

You can create your own styles [here](https://mapstyle.withgoogle.com/).

Be aware, after creating your custom style, save the generated code in a .json file into the styles folder.

### Preview of pre-defined styles:

**Aubergine style:**
!["NagMap Reborn Styles"](https://i.imgur.com/FAU7lOp.png "Aubergine style")

**Dark style:**
!["NagMap Reborn Styles"](https://i.imgur.com/QPeh3AN.png "Dark style")

**Night style:**
!["NagMap Reborn Styles"](https://i.imgur.com/EaArsvB.png "Night style")

**Retro style:**
!["NagMap Reborn Styles"](https://i.imgur.com/GWxQc23.png "Retro style")

**Silve style:**
!["NagMap Reborn Styles"](https://i.imgur.com/hiEQEJU.png "Silve style")


## Priority of choice.

If you set the style variable, the style you choose will override any previously defined options in the map type variable:
```PHP
$nagMapR_MapType
```
If you did not want to use custom styles, leave the variable empty:
```PHP
$nagMapR_Style = '';
```