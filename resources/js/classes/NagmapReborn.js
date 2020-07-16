/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

class NagmapReborn {

    constructor() {
        try {

            this._u('Creating and loading map.');
            this.insertMap();
            this._u('Map finished.');

            this._u('Creating and loading extra elemnets.');
            this.createExtras();
            this._u('Extras finished.');

            this._u('Creating and loading all icons');
            this.icons = this.getIcons();
            this._u('Icons finished.');

            this._u('Creating and loading OMS instance');
            this.oms = new OverlappingMarkerSpiderfier(window.map, {
                keepSpiderfied: true,
                spiralFootSeparation: 28,
                nearbyDistance: 40
            });

            this.oms.addListener('click', (marker) => {
                openPopup(marker);
            });
            this._u('OMS finished.');

            this._u('Creating and loading hosts');
            this.hosts = this.getHosts();
            this._u('Hosts finished.');

            this._u('Creating and loading lines');
            this.createLines();
            this._u('Lines finished.');

            this._u("Final touches.");
            this.initChangesBar();
            let cred = $("div.leaflet-control-attribution.leaflet-control").first();
            cred.html("<a href='https://github.com/jocafamaka/nagmapReborn' target='_blank' title='Nagmap Reborn'>Nagmap Reborn</a> Server monitoring | " + cred.html());

            this.initReporting();

            $('#filter_str').keyup(() => {
                nagmapReborn.search();
            });

            if (config.soundAlert)
                config.alertSound = new Audio('resources/alert.mp3');

            window.generalStatus = STATUS.GENERAL.finished;

            // justAnErro();

            setInterval(() => {
                nagmapReborn.updateStatus();
            }, config.updateTime * 1000);

            this._u('Nagmap Reborn was successfully initialized.');

        } catch (e) {
            Utils.initErrorHandler(e, i18next.t('ngr_init_error'));
        }
    }

    /** 
     * Responsible for creating the map.
     * @return undefined
     */
    insertMap() {

        if (config.cbMode) {
            if (!config.cbSize || config.cbSize > 50 || config.cbSize < 25) {
                config.cbSize = (config.cbSize > 50) ? 50 : 25;
            }
            if (config.cbMode !== 3) {
                $("#map").css("height", `${100 - config.cbSize}%`, "important");
            } else {
                $("#map").addClass("mapdb3").css("width", `${100 - config.cbSize}%`, "important").css("float", "left", "important");
            }
        }

        window.map = L.map('map', {
            zoomControl: false
        }).setView(config.mapCenter, config.mapDefaultZoom);

        if (config.mapTiles) {
            L.tileLayer(config.mapTiles, {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors.'
            }).addTo(window.map);
        }
    }

    /**
     * Responsible for insert ChangesBar and Filter. 
     * @return undefined
     */
    createExtras() {
        if (config.cbMode) {
            this._u('Creating and loading ChangesBar.');
            $("#changesbar").css("font-size", `${config.cbFontSize}px`, "important");

            if (config.cbMode !== 3) {
                $("#changesbar").css("height", `${config.cbSize}%`, "important").css("display", "block", "important");
            } else {
                $("#changesbar").addClass("db3").css("height", "100%", "important").css("display", "block", "important");
            }

            if (config.cbFilter) {
                $("#filter").html(`
                <div class="row" style="width:100%;">
                    <div class="row">
                        <div class="input-field col s10" style="margin-bottom: 0px !important;">
                        <input id="filter_str" type="text" class="validate" style="font-size:${config.cbFontSize}px;">
                        <label for="filter_str">${i18next.t('filter')}</label>
                        </div>
                        <div class="input-field col s2">
                        <button style="width:100%" class="btn waves-effect waves-light red lighten-1" title="${i18next.t('clear')}" onclick="e=>{e.preventDefault();};$('#filter_str').val('');M.updateTextFields();nagmapReborn.search();"><i class="material-icons">delete</i></button>
                        </div>
                    </div>
                    <div id="filterPrepend">
                    </div>
                </div>
                `);

                $("#filter").css("display", "flex", "important");
            }
        }

        if (config.debug) {
            this._u('Creating and loading debug elements.');
            $("#debug").html(`
            <a onclick="$('#debug_console').toggleClass('open');setTimeout(() => {$('#console_text').getNiceScroll().resize();}, 1006);" class="waves-effect waves-light btn btn-large cyan darken-3 button"><i class="material-icons left">featured_play_list</i>${i18next.t('debug_csl')}</a>
            `);
            /* <a onclick="location.href='debugInfo/index.php'" class="waves-effect waves-light btn btn-large green darken-3 button"><i class="material-icons left">assignment</i>${i18next.t('debug_pg')}</a> */
            $("#console_text").niceScroll({
                background: "rgba(0,0,0,0)",
                cursorcolor: "rgba(9, 255, 0,0.75)",
                cursorwidth: "4px",
                cursorborder: "0px",
            });
        }
    }

    /**
     * Responsible for load all defined icons. 
     * @return Object icons
     */
    getIcons() {

        if (config.icons == null) {
            // this._u("Unable to load icon styles, please make sure that resources/icons/icons.json exists and is a valid json.", false);
            throw i18next.t("load_icons_error") || "Unable to load icon styles, please make sure that resources/icons/icons.json exists and is a valid json.";
        }

        if (!config.icons.styles.hasOwnProperty(config.defaultIconStyle)) {
            this._u(i18next.t("load_icon_style_error", { t: config.defaultIconStyle }), false);
            config.defaultIconStyle = "marker_shadow";
        }

        let icons = {};

        icons.red = L.icon({
            iconUrl: "resources/icons/styles/" + config.icons.styles[config.defaultIconStyle].red.iconUrl,
            iconSize: config.icons.styles[config.defaultIconStyle].red.iconSize,
            iconAnchor: (config.icons.styles[config.defaultIconStyle].red.iconAnchor || [Math.floor((parseInt(config.icons.styles[config.defaultIconStyle].red.iconSize[0]) / 2)), parseInt(config.icons.styles[config.defaultIconStyle].red.iconSize[1]) - 1]),
            popupAnchor: (config.icons.styles[config.defaultIconStyle].red.popupAnchor || [0, -(parseInt(config.icons.styles[config.defaultIconStyle].red.iconSize[1]) - 1)])
        });

        icons.green = L.icon({
            iconUrl: "resources/icons/styles/" + config.icons.styles[config.defaultIconStyle].green.iconUrl,
            iconSize: config.icons.styles[config.defaultIconStyle].green.iconSize,
            iconAnchor: (config.icons.styles[config.defaultIconStyle].green.iconAnchor || [Math.floor((parseInt(config.icons.styles[config.defaultIconStyle].green.iconSize[0]) / 2)), parseInt(config.icons.styles[config.defaultIconStyle].green.iconSize[1]) - 1]),
            popupAnchor: (config.icons.styles[config.defaultIconStyle].green.popupAnchor || [0, -(parseInt(config.icons.styles[config.defaultIconStyle].green.iconSize[1]) - 1)])
        });

        icons.orange = L.icon({
            iconUrl: "resources/icons/styles/" + config.icons.styles[config.defaultIconStyle].orange.iconUrl,
            iconSize: config.icons.styles[config.defaultIconStyle].orange.iconSize,
            iconAnchor: (config.icons.styles[config.defaultIconStyle].orange.iconAnchor || [Math.floor((parseInt(config.icons.styles[config.defaultIconStyle].orange.iconSize[0]) / 2)), parseInt(config.icons.styles[config.defaultIconStyle].orange.iconSize[1]) - 1]),
            popupAnchor: (config.icons.styles[config.defaultIconStyle].orange.popupAnchor || [0, -(parseInt(config.icons.styles[config.defaultIconStyle].orange.iconSize[1]) - 1)])
        });

        icons.yellow = L.icon({
            iconUrl: "resources/icons/styles/" + config.icons.styles[config.defaultIconStyle].yellow.iconUrl,
            iconSize: config.icons.styles[config.defaultIconStyle].yellow.iconSize,
            iconAnchor: (config.icons.styles[config.defaultIconStyle].yellow.iconAnchor || [Math.floor((parseInt(config.icons.styles[config.defaultIconStyle].yellow.iconSize[0]) / 2)), parseInt(config.icons.styles[config.defaultIconStyle].yellow.iconSize[1]) - 1]),
            popupAnchor: (config.icons.styles[config.defaultIconStyle].yellow.popupAnchor || [0, -(parseInt(config.icons.styles[config.defaultIconStyle].yellow.iconSize[1]) - 1)])
        });

        icons.grey = L.icon({
            iconUrl: "resources/icons/styles/" + config.icons.styles[config.defaultIconStyle].grey.iconUrl,
            iconSize: config.icons.styles[config.defaultIconStyle].grey.iconSize,
            iconAnchor: (config.icons.styles[config.defaultIconStyle].grey.iconAnchor || [Math.floor((parseInt(config.icons.styles[config.defaultIconStyle].grey.iconSize[0]) / 2)), parseInt(config.icons.styles[config.defaultIconStyle].grey.iconSize[1]) - 1]),
            popupAnchor: (config.icons.styles[config.defaultIconStyle].grey.popupAnchor || [0, -(parseInt(config.icons.styles[config.defaultIconStyle].grey.iconSize[1]) - 1)])
        });

        return icons;
    }

    /**
     * Responsible for load all valid hosts.
     * @return Array hosts
     */
    getHosts() {
        let tempHosts = [];

        for (let h in config.initialHosts)
            tempHosts[h] = new Host(h, config.initialHosts[h], this.icons, this.oms);

        return tempHosts;
    }

    /**
     * Responsible for create all lines. 
     * @return undefined
     */
    createLines() {
        if (config.showLines) {
            for (let h in this.hosts) {

                let lineColor = "#A9ABAE";

                if (this.hosts[h].currentStatus === STATUS.HOSTS.up) {
                    lineColor = "#007f00";
                } else if (this.hosts[h].currentStatus === STATUS.HOSTS.warning) {
                    lineColor = "#ffff00";
                } else if (this.hosts[h].currentStatus === STATUS.HOSTS.critical) {
                    lineColor = "#d25700";
                } else if (this.hosts[h].currentStatus === STATUS.HOSTS.down) {
                    lineColor = "#c92a2a";
                }

                if (this.hosts[h].parents) {
                    this.hosts[h].parents.forEach(p => {
                        if (this.hosts[p]) {
                            this._u(`Creating line from {${this.hosts[h].latlng}} to {${this.hosts[p].latlng}}`);
                            this.hosts[h].lines.push(new L.Polyline([this.hosts[h].latlng, this.hosts[p].latlng], {
                                color: lineColor,
                                weight: 1.5,
                                opacity: 0.8,
                                smoothFactor: 1
                            }).addTo(window.map));
                        }
                    });
                }
            }
        }
    }

    /**
     * Responsible for insert initial status on changesBar. 
     * @return undefined
     */
    initChangesBar() {
        if (config.cbMode && config.cbMode != 1) {
            for (let h in this.hosts) {
                let status;
                if (this.hosts[h].currentStatus == STATUS.HOSTS.warning) {
                    status = "WAR";
                }
                if (this.hosts[h].currentStatus == STATUS.HOSTS.critical) {
                    status = "CRIT";
                }
                if (this.hosts[h].currentStatus == STATUS.HOSTS.down) {
                    status = "DOWN";
                }
                if (status)
                    $(`#${status.toLowerCase()}Hosts`).prepend(`<div onclick="openPopup('${h}');" class="changesBarLine ${status} news" id="${h}-${status}" style="opacity:1; max-height: 80px;">${this.hosts[h].alias} - (${i18next.t('waiting')})</div>`);
            }
        }
    }

    /**
     * Responsible for update a host status, icon and lines. 
     * @return undefined
     */
    updateHost(host, newStatus) {
        _u(`(Host): Update {${host}} to status {${newStatus}}`);
        let icon = this.icons.grey;
        let time = 1;
        let color = "#A9ABAE";
        let zIndex = config.priorities.unknown;

        if (newStatus === STATUS.HOSTS.up) {
            icon = this.icons.green;
            zIndex = config.priorities.up;
            color = "#007f00";
        } else if (newStatus === STATUS.HOSTS.warning) {
            icon = this.icons.yellow;
            zIndex = config.priorities.warning;
            color = "#ffff00";
        } else if (newStatus === STATUS.HOSTS.critical) {
            icon = this.icons.orange;
            zIndex = config.priorities.critical;
            color = "#d25700";
        } else if (newStatus === STATUS.HOSTS.down) {
            icon = this.icons.red;
            zIndex = config.priorities.down;
            time = 20;
            color = "#c92a2a";
            if (config.soundAlert)
                config.alertSound.play();
        }

        if (host in nagmapReborn.hosts) {
            nagmapReborn.hosts[host].updateStatus(icon, time, zIndex, color);
            nagmapReborn.hosts[host].currentStatus = newStatus;
        }
    }

    /**
     * Responsible for update a host info on changesBar. 
     * @return undefined
     */
    updateChangesBar(host, hostName, newData) {
        if (config.cbMode) {
            if (config.cbMode == 1) {

                if (newData.status != host.currentStatus) {
                    let status = "unknown",
                        oldStatus = "unknown",
                        className = "UNK",
                        timestamp = Date.now();

                    if (newData.status == STATUS.HOSTS.up) {
                        status = "up";
                        className = "UP";
                    } else if (newData.status == STATUS.HOSTS.warning) {
                        status = "warning";
                        className = "WAR";
                    } else if (newData.status == STATUS.HOSTS.critical) {
                        status = "critical";
                        className = "CRIT";
                    } else if (newData.status == STATUS.HOSTS.down) {
                        status = "down";
                        className = "DOWN";
                    }

                    if (host.currentStatus === STATUS.HOSTS.up) {
                        oldStatus = "up";
                    } else if (host.currentStatus === STATUS.HOSTS.warning) {
                        oldStatus = "warning";
                    } else if (host.currentStatus === STATUS.HOSTS.critical) {
                        oldStatus = "critical";
                    } else if (host.currentStatus === STATUS.HOSTS.down) {
                        oldStatus = "down";
                    }

                    $((config.cbFilter ? '#filterPrepend' : '#changesbar')).prepend(`<div onclick="openPopup('${hostName}');" class="changesBarLine ${className} news" id="${hostName}-${timestamp}" style="opacity:0; max-height: 0px;">${Utils.now()} - ${host.alias}: ${i18next.t(oldStatus)} → ${i18next.t(status)}</div>`);

                    setTimeout((el) => {
                        el.css('max-height', "100px");
                        el.css('opacity', "1");
                    }, 100, $(`#${hostName}-${timestamp}`));
                }
            }
            else {
                let status, oldStatus;
                if (newData.status == STATUS.HOSTS.warning) {
                    status = "WAR";
                } else if (newData.status == STATUS.HOSTS.critical) {
                    status = "CRIT";
                } else if (newData.status == STATUS.HOSTS.down) {
                    status = "DOWN";
                }
                if (newData.status != host.currentStatus) {
                    if (host.currentStatus === STATUS.HOSTS.warning) {
                        oldStatus = "WAR";
                    } else if (host.currentStatus === STATUS.HOSTS.critical) {
                        oldStatus = "CRIT";
                    } else if (host.currentStatus === STATUS.HOSTS.down) {
                        oldStatus = "DOWN";
                    }

                    if (status) {
                        $(`#${status.toLowerCase()}Hosts`).prepend(`<div onclick="openPopup('${hostName}');" class="changesBarLine ${status} news" id="${hostName}-${status}" style="opacity:0; max-height: 0px;">${host.alias} - ${i18next.t('timePrefix')} ${newData.time} ${i18next.t('timeSuffix')}</div>`);

                        setTimeout((el) => {
                            el.css('max-height', "100px");
                            el.css('opacity', "1");
                        }, 100, $(`#${hostName}-${status}`));
                    }

                    if (oldStatus) {
                        let el = $(`#${hostName}-${oldStatus}`);
                        el.css('max-height', "0px");
                        el.css('opacity', "0");

                        setTimeout((el) => {
                            el.remove();
                        }, 900, el);
                    }
                }
                else {
                    $(`#${hostName}-${status}`).html(`${host.alias} - ${i18next.t('timePrefix')} ${newData.time} ${i18next.t('timeSuffix')}`);
                }
            }
            nagmapReborn.search();
        }
    }

    /**
     * Responsible for search and filter hosts on changes bar. 
     * @return undefined
     */
    search() {
        if (config.cbFilter) {
            let query = $('#filter_str').val().toLowerCase();
            let selector = (config.cbMode == 1 ? '#filterPrepend .changesBarLine' : '#changesbar .changesBarLine');
            $(selector).each((i, el) => {
                if ($(el).text().toLowerCase().indexOf(query) === -1) {
                    $(el).closest(selector).hide();
                } else {
                    $(el).closest(selector).show();
                }
            });
        }
    };

    /**
     * Responsible for get updated status. 
     * @return undefined
     */
    updateStatus() {
        this._u("Update status called.");
        let hosts = [];
        for (let h in nagmapReborn.hosts) {
            hosts.push([h, nagmapReborn.hosts[h].currentStatus])
        }

        let params = new URLSearchParams();
        params.append('key', config.secKey);
        params.append('hosts', JSON.stringify(hosts));

        axios.post(`update.php?${Utils.getFullQueryString()}`, params)
            .then((response) => {
                if (response.data.missing == true) {
                    config.realTime = false;
                    Utils.showToastr("warning", i18next.t("updateMissing"));
                    console.warn(i18next.t("updateErrorChanges"));
                    this._u(i18next.t("updateErrorChanges"), false);

                } else if (config.realTime == false) {
                    config.realTime = true;
                    Utils.showToastr("success", i18next.t("updateErrorSolved"));

                }
                let hosts = response.data.hosts;
                for (let [hostName, data] of Object.entries(hosts)) {
                    if (hostName in nagmapReborn.hosts) {
                        nagmapReborn.updateChangesBar(nagmapReborn.hosts[hostName], hostName, data);

                        if (nagmapReborn.hosts[hostName].currentStatus != data.status) {
                            nagmapReborn.updateHost(hostName, data.status);
                        }
                    }
                }
            })
            .catch((error) => {
                config.realTime = false;

                if (typeof error.response !== 'undefined' && error.response.status == 401) {
                    Utils.showToastr("error", i18next.t("updateErrorDenied"));
                    console.warn(`${i18next.t("updateErrorDenied")} (${error})`);
                    this._u(`${i18next.t("updateErrorDenied")} (${error})`, false);
                } else {
                    Utils.showToastr("error", i18next.t("updateError"));
                    console.warn(`${i18next.t("updateErrorServ")} (${error})`);
                    this._u(`${i18next.t("updateErrorServ")} (${error})`, false);
                }

            });
    }

    /**
     * Responsible for check Nagmap Reborn updates. 
     * @return undefined
     */
    checkNgRebornUpdate() {
        this._u("Checking for updates.");
        axios.get("https://raw.githubusercontent.com/jocafamaka/nagmapReborn/master/VERSION")
            .then(response => {
                if (config.ngRebornVersion != null && (config.ngRebornVersion != response.data)) {
                    Swal.fire({
                        heightAuto: false,
                        icon: 'info',
                        title: `${i18next.t('newVersion')}!<br>v${response.data}`,
                        html: `${i18next.t('newVersionText')}<center><a href="https://github.com/jocafamaka/nagmapReborn/releases" target="_blank" style="cursor: pointer;"><img title="<${i18next.t('project')}" src="resources/img/logoBlack.png" alt=""></a><center>`,
                        confirmButtonText: i18next.t('close'),
                        timer: 10000,
                        footer: `<small>${i18next.t('newVersionFooter')}</small>`
                    }).then(() => {
                        window.nagmapReborn.checkDefaultAuth();
                    })
                }
                else {
                    window.nagmapReborn.checkDefaultAuth();
                }
            })
            .catch(error => {
                this._u(`It was not possible to check if there is an update! (${error})`, false);
                console.warn(`It was not possible to check if there is an update! (${error})`);
                window.nagmapReborn.checkDefaultAuth();
            });
    }

    /**
     * Responsible for warning about default user and password. 
     * @return undefined
     */
    checkDefaultAuth() {
        if (config.defaultAuth) {
            Swal.fire({
                heightAuto: false,
                icon: 'warning',
                title: i18next.t('passAlertTitle'),
                text: i18next.t('passAlert'),
                confirmButtonText: 'OK'
            })
        }
    }

    initReporting() {
        if (config.reporting) {
            axios.get("https://raw.githubusercontent.com/jocafamaka/nagmapReborn/developing/resources/reporter/DOMAIN")
                .then(response => {
                    config.domain = response.data;
                })
                .catch(error => {
                    // 
                }).then(() => {
                    var _paq = window._paq || [];
                    _paq.push(["setDocumentTitle", document.domain + "/" + document.title]); _paq.push(["setCustomVariable", 1, "versao", config.ngRebornVersion, "visit"]); _paq.push(["trackPageView"]); _paq.push(["enableLinkTracking"]); (function () { var u = `https://${config.domain}/analytics/`; _paq.push(["setTrackerUrl", u + "piwik.php"]); _paq.push(["setSiteId", "2"]); var d = document, g = d.createElement("script"), s = d.getElementsByTagName("script")[0]; g.type = "text/javascript"; g.async = true; g.defer = true; g.src = u + "piwik.js"; s.parentNode.insertBefore(g, s); })();
                });
        }
    }

    /**
     * Decorator to debug console. 
     * @return undefined
     */
    _u(msg, st = true) {
        _u(`(${this.constructor.name}): ${msg}`, st);
    }
}