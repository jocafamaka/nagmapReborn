/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

class NagmapReborn {

    constructor(config = {}) {
        try {
            // Load config
            this._debug = config.debug || 0;
            this._mapCenter = config.mapCenter || [-6.469293, -50.913464];
            this._mapDefaultZoom = config.mapDefaultZoom || 6.1;
            this._mapTiles = config.mapTiles || "//{s}.tile.osm.org/{z}/{x}/{y}.png";
            this._locale = config.locale || "en-US";
            this._cbMode = config.cbMode || 0;
            this._cbSize = config.cbSize || 25;
            this._cbFilter = config.cbFilter || 0;
            this._cbFontSize = config.cbFontSize || 25;
            this._dtFormat = config.dtFormat || 1;
            this._soundAlert = config.soundAlert || 0;
            this._iconStyle = config.iconStyle || 0;
            this._showLines = config.showLines || 0;
            this._updateTime = config.updateTime || 15;
            this._secKey = config.secKey || "s9Yqz7Ox9pgpYx5cVinh7Iez4ZY29KGqqx9SlxSDbxmRHWgkjuLjogOIz4WFGuFQy2EOwKBJo6AA5UQY1IArMgsiR7KQwXyB";

            this.insertMap();

            this.createExtras();

            this.icons = this.getIcons();

            // Create oms instance.
            this.oms = new OverlappingMarkerSpiderfier(window.map, {
                keepSpiderfied: true,
                spiralFootSeparation: 28,
                nearbyDistance: 40
            });

            this.oms.addListener('click', function (marker) {
                openPopup(marker);
            });

            this.hosts = this.getHosts();

            this.createLines();

            // justAnError();

            // window.generalStatus = 1;
        } catch (e) {
            Utils.initErrorHandler(e);
        }
    }

    /* 
     *  Insert a new script/link in page head.
     */
    /* insertNode(ref) {
        let el;
        if (cf[0]) {
            el = document.createElement('script');
            el.src = ref;
        } else {
            el = document.createElement('link');
            el.rel = "stylesheet";
            el.href = ref;
        }
        document.getElementsByTagName("head")[0].appendChild(el);
    } */

    /** 
     * Responsible for creating the map.
     * @return undefined
     */
    insertMap() {
        /* this.insertNode("resources/leaflet/leaflet.css");
        this.insertNode("resources/leaflet/leaflet.js");
        this.insertNode("resources/leaflet/oms.js");
        this.insertNode("resources/leaflet/leaflet.smoothmarkerbouncing.js"); */

        if (this._cbMode) {
            if (!this._cbSize || this._cbSize > 50 || this._cbSize < 25) {
                (this._cbSize > 50) ? this._cbSize = 50: this._cbSize = 25;
            }
            if (this._cbMode !== 3) {
                $("#map").css("height", `${100 - this._cbSize}%`, "important");
            } else {
                $("#map").css("width", `${100 - this._cbSize}%`, "important").css("float", "left", "important");
            }
        }

        window.map = L.map('map', {
            zoomControl: false
        }).setView(this._mapCenter, this._mapDefaultZoom);

        if (this._mapTiles) {
            L.tileLayer(this._mapTiles, {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors.'
            }).addTo(window.map);
        }
    }

    /**
     * Responsible for insert ChangesBar and Filter. 
     * @return undefined
     */
    createExtras() {
        if (this._cbMode) {

            if (this._cbMode !== 3) {
                $("#changesbar").css("height", `${this._cbSize}%`, "important").css("display", "block", "important");
            } else {
                $("#changesbar").css("height", "100%", "important").css("display", "block", "important");
            }

            if (this._cbFilter) {
                $("#filter").html(`
                <input style="font-size:${this._cbFontSize}px;" type="text" id="searchBar" class="form-control" placeholder="${i18next.t('filter')}...">
                <button class="cleanS" onclick="e=>{e.preventDefault();};$('#searchBar').val('');search();" style="font-size:${this._cbFontSize}px;" title="${i18next.t('clear')}">
                    <img alt="" src="resources/img/trash-alt-regular.svg" height="${this._cbFontSize}px">
                </button>
                <div class="clearfix"></div>
                `);

                $("#filter").css("display", "flex", "important");
            }
        }

        if (this._debug) {
            $("#debug").html(`
            <button onclick="location.href='debugInfo/index.php'" class="button">
                <span>${i18next.t('debug_pg')}</span>
            </button>
            `);
        }
    }

    /**
     * Responsible for load all defined icons. 
     * @return Object icons
     */
    getIcons() {

        let icons = {};

        icons.red = L.icon({
            iconUrl: `resources/img/icons/MarkerRedSt-${this._iconStyle}.png`,
            iconSize: [29, 43],
            iconAnchor: [14, 42],
            popupAnchor: [0, -42]
        });

        icons.green = L.icon({
            iconUrl: `resources/img/icons/MarkerGreenSt-${this._iconStyle}.png`,
            iconSize: [29, 43],
            iconAnchor: [14, 42],
            popupAnchor: [0, -42]
        });

        icons.orange = L.icon({
            iconUrl: `resources/img/icons/MarkerOrangeSt-${this._iconStyle}.png`,
            iconSize: [29, 43],
            iconAnchor: [14, 42],
            popupAnchor: [0, -42]
        });

        icons.yellow = L.icon({
            iconUrl: `resources/img/icons/MarkerYellowSt-${this._iconStyle}.png`,
            iconSize: [29, 43],
            iconAnchor: [14, 42],
            popupAnchor: [0, -42]
        });

        icons.grey = L.icon({
            iconUrl: `resources/img/icons/MarkerGreySt-${this._iconStyle}.png`,
            iconSize: [29, 43],
            iconAnchor: [14, 42],
            popupAnchor: [0, -42]
        });

        return icons;
    }

    /**
     * Responsible for load all valid hosts.
     * @return Array hosts
     */
    getHosts() {
        let tempHosts = [];

        for (let h in tempHostsInfo)
            tempHosts[h] = new Host(tempHostsInfo[h], this.icons, this.oms);

        tempHostsInfo = [];

        return tempHosts;
    }

    /**
     * Responsible for create all lines. 
     * @return undefined
     */
    createLines() {
        for (let h in this.hosts) {

            let lineColor = "#A9ABAE";

            if (this.hosts[h].currentStatus === 0) {
                lineColor = "#007f00";
            } else if (this.hosts[h].currentStatus === 1) {
                lineColor = "#ffff00";
            } else if (this.hosts[h].currentStatus === 2) {
                lineColor = "#d25700";
            } else if (this.hosts[h].currentStatus === 3) {
                lineColor = "#c92a2a";
            }

            if (this.hosts[h].parents) {
                this.hosts[h].parents.forEach(p => {
                    if (this.hosts[p]) {
                        this.hosts[h].lines.push(new L.Polyline([this.hosts[h].latlng, this.hosts[p].latlng], {
                            color: lineColor,
                            weight: 1.5,
                            opacity: 0.8,
                            smoothFactor: 1
                        }).addTo(window.map));
                    }
                });
            }

            /* for (let p in this.hosts[h].parents) {
                console.log(p)
                this.hosts[h].lines.push(new L.Polyline([this.hosts[h].latlng, this.hosts[p].latlng], {
                    color: lineColor,
                    weight: 1.5,
                    opacity: 0.8,
                    smoothFactor: 1
                }).addTo(window.map));
            } */
        }
    }

    /*  DEFINE GETTERS AND SETTERS  */
    get debug() {
        return this._debug;
    }
    get mapCenter() {
        return this._mapCenter;
    }
    get mapDefaultZoom() {
        return this._mapDefaultZoom;
    }
    get locale() {
        return this._locale;
    }
    get cbMode() {
        return this._cbMode;
    }
    get cbSize() {
        return this._cbSize;
    }
    get cbFontSize() {
        return this._cbFontSize;
    }
    get dtFormat() {
        return this._dtFormat;
    }
    get soundAlert() {
        return this._soundAlert;
    }
    get IconStyle() {
        return this._IconStyle;
    }
    get Lines() {
        return this._Lines;
    }
    get updateTime() {
        return this._updateTime;
    }
    get secKey() {
        return this._secKey;
    }
    set debug(v) {
        this._debug = v;
    }
    set mapCenter(v) {
        this._mapCenter = v;
    }
    set mapDefaultZoom(v) {
        this._mapDefaultZoom = v;
    }
    set locale(v) {
        this._locale = v;
    }
    set cbMode(v) {
        this._cbMode = v;
    }
    set cbSize(v) {
        this._cbSize = v;
    }
    set cbFontSize(v) {
        this._cbFontSize = v;
    }
    set dtFormat(v) {
        this._dtFormat = v;
    }
    set soundAlert(v) {
        this._soundAlert = v;
    }
    set IconStyle(v) {
        this._IconStyle = v;
    }
    set Lines(v) {
        this._Lines = v;
    }
    set updateTime(v) {
        this._updateTime = v;
    }
    set secKey(v) {
        this._secKey = v;
    }
    /*  END GETTERS AND SETTERS  */
}