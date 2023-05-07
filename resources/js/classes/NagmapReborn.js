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

            this._u('Creating and loading all icons styles');
            this.loadIcons();
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
            cred.html("<a href='https://github.com/jocafamaka/nagmapReborn' target='_blank' title='Nagmap Reborn'>Nagmap Reborn</a> - Server monitoring | " + cred.html());

            this.initReporting();

            $('#filter_str').keyup(() => {
                this.search();
            });

            if (config.sound_alert)
                config.alertSound = new Audio('resources/alert.mp3');

            window.generalStatus = STATUS.GENERAL.finished;

            // justAnErro();

            setInterval(() => {
                this.updateStatus();
            }, config.update_time * 1000);

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

        if (config.changes_bar.mode) {
            if (!config.changes_bar.size || config.changes_bar.size > 50 || config.changes_bar.size < 25) {
                config.changes_bar.size = (config.changes_bar.size > 50) ? 50 : 25;
            }
            if (config.changes_bar.mode !== 3) {
                $("#map").css("height", `${100 - config.changes_bar.size}%`, "important");
            } else {
                $("#map").addClass("mapdb3").css("width", `${100 - config.changes_bar.size}%`, "important").css("float", "left", "important");
            }
        }

        window.map = L.map('map', {
            zoomControl: false
        }).setView(config.map.center, config.map.default_zoom);

        L.tileLayer((config.map.tiles || "//{s}.tile.osm.org/{z}/{x}/{y}.png"), {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors.'
        }).addTo(window.map);

    }

    /**
     * Responsible for insert ChangesBar and Filter. 
     * @return undefined
     */
    createExtras() {
        if (config.changes_bar.mode) {
            this._u('Creating and loading ChangesBar.');
            $("#changesbar").css("font-size", `${config.changes_bar.font_size}px`, "important");

            if (config.changes_bar.mode !== 3) {
                $("#changesbar").css("height", `${config.changes_bar.size}%`, "important").css("display", "block", "important");
            } else {
                $("#changesbar").addClass("db3").css("height", "100%", "important").css("display", "block", "important");
            }

            if (config.changes_bar.filter) {
                $("#filter").html(`
                <div class="row" style="width:100%;">
                    <div class="row">
                        <div class="input-field col s10" style="margin-bottom: 0px !important;">
                        <input id="filter_str" type="text" class="validate" style="font-size:${config.changes_bar.font_size}px;">
                        <label for="filter_str">${i18next.t('filter')}</label>
                        </div>
                        <div class="input-field col s2">
                        <button style="width:100%" class="btn waves-effect waves-light red lighten-1" title="${i18next.t('clear')}" onclick="e=>{e.preventDefault();};$('#filter_str').val('');M.updateTextFields();this.search();"><i class="material-icons">delete</i></button>
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
    loadIcons() {

        if (config.icons == null) {
            throw i18next.t("load_icons_error") || "Unable to load icon styles, please make sure that resources/icons/icons.json exists and is a valid json.";
        }

        if (config.custom_icons != null) {
            this._u(i18next.t("load_custom_icons") || "Loading custom icon definitions from custom_icons.json file.Unable to load icon styles, please make sure that resources/icons/icons.json exists and is a valid json.");
            ["names", "hostgroups", "styles"].forEach(node => {
                if (config.custom_icons.hasOwnProperty(node)) {
                    for (let subnode in config.custom_icons[node]) {
                        config.icons[node][subnode] = config.custom_icons[node][subnode];
                    }
                }
            });
        }

        let icons = {};

        for (let style_name in config.icons.styles) {
            this._u("Style: " + style_name);

            icons[style_name] = {
                "red": L.icon({
                    iconUrl: "resources/icons/styles/" + config.icons.styles[style_name].red.iconUrl,
                    iconSize: config.icons.styles[style_name].red.iconSize,
                    iconAnchor: (config.icons.styles[style_name].red.iconAnchor || [Math.floor((parseInt(config.icons.styles[style_name].red.iconSize[0]) / 2)), parseInt(config.icons.styles[style_name].red.iconSize[1]) - 1]),
                    popupAnchor: (config.icons.styles[style_name].red.popupAnchor || [0, -(parseInt(config.icons.styles[style_name].red.iconSize[1]) - 1)])
                }),

                "green": L.icon({
                    iconUrl: "resources/icons/styles/" + config.icons.styles[style_name].green.iconUrl,
                    iconSize: config.icons.styles[style_name].green.iconSize,
                    iconAnchor: (config.icons.styles[style_name].green.iconAnchor || [Math.floor((parseInt(config.icons.styles[style_name].green.iconSize[0]) / 2)), parseInt(config.icons.styles[style_name].green.iconSize[1]) - 1]),
                    popupAnchor: (config.icons.styles[style_name].green.popupAnchor || [0, -(parseInt(config.icons.styles[style_name].green.iconSize[1]) - 1)])
                }),

                "orange": L.icon({
                    iconUrl: "resources/icons/styles/" + config.icons.styles[style_name].orange.iconUrl,
                    iconSize: config.icons.styles[style_name].orange.iconSize,
                    iconAnchor: (config.icons.styles[style_name].orange.iconAnchor || [Math.floor((parseInt(config.icons.styles[style_name].orange.iconSize[0]) / 2)), parseInt(config.icons.styles[style_name].orange.iconSize[1]) - 1]),
                    popupAnchor: (config.icons.styles[style_name].orange.popupAnchor || [0, -(parseInt(config.icons.styles[style_name].orange.iconSize[1]) - 1)])
                }),

                "yellow": L.icon({
                    iconUrl: "resources/icons/styles/" + config.icons.styles[style_name].yellow.iconUrl,
                    iconSize: config.icons.styles[style_name].yellow.iconSize,
                    iconAnchor: (config.icons.styles[style_name].yellow.iconAnchor || [Math.floor((parseInt(config.icons.styles[style_name].yellow.iconSize[0]) / 2)), parseInt(config.icons.styles[style_name].yellow.iconSize[1]) - 1]),
                    popupAnchor: (config.icons.styles[style_name].yellow.popupAnchor || [0, -(parseInt(config.icons.styles[style_name].yellow.iconSize[1]) - 1)])
                }),

                "grey": L.icon({
                    iconUrl: "resources/icons/styles/" + config.icons.styles[style_name].grey.iconUrl,
                    iconSize: config.icons.styles[style_name].grey.iconSize,
                    iconAnchor: (config.icons.styles[style_name].grey.iconAnchor || [Math.floor((parseInt(config.icons.styles[style_name].grey.iconSize[0]) / 2)), parseInt(config.icons.styles[style_name].grey.iconSize[1]) - 1]),
                    popupAnchor: (config.icons.styles[style_name].grey.popupAnchor || [0, -(parseInt(config.icons.styles[style_name].grey.iconSize[1]) - 1)])
                })
            }
        }

        config.icons.styles = icons;
    }

    /**
     * Responsible for load all valid hosts.
     * @return Array hosts
     */
    getHosts() {
        let tempHosts = [];

        for (let h in config.initial_hosts)
            tempHosts[h] = new Host(h, config.initial_hosts[h], this.oms);

        return tempHosts;
    }

    /**
     * Responsible for create all lines. 
     * @return undefined
     */
    createLines() {
        if (config.show_lines) {
            for (let h in this.hosts) {

                let lineColor = STATUS.COLORS.unknown;

                if (this.hosts[h].currentStatus === STATUS.HOSTS.up) {
                    lineColor = STATUS.COLORS.up;
                } else if (this.hosts[h].currentStatus === STATUS.HOSTS.warning) {
                    lineColor = STATUS.COLORS.warning;
                } else if (this.hosts[h].currentStatus === STATUS.HOSTS.critical) {
                    lineColor = STATUS.COLORS.critical;
                } else if (this.hosts[h].currentStatus === STATUS.HOSTS.down) {
                    lineColor = STATUS.COLORS.down;
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
        if (config.changes_bar.mode && config.changes_bar.mode != 1) {
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
     * Responsible for update a host info on changesBar. 
     * @return undefined
     */
    updateChangesBar(host, hostName, newData) {
        if (config.changes_bar.mode) {
            if (config.changes_bar.mode == 1) {

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

                    $((config.changes_bar.filter ? '#filterPrepend' : '#changesbar')).prepend(`<div onclick="openPopup('${hostName}');" class="changesBarLine ${className} news" id="${hostName}-${timestamp}" style="opacity:0; max-height: 0px;">${Utils.now()} - ${host.alias}: ${i18next.t(oldStatus)} → ${i18next.t(status)}</div>`);

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
            this.search();
        }
    }

    /**
     * Responsible for search and filter hosts on changes bar. 
     * @return undefined
     */
    search() {
        if (config.changes_bar.filter) {
            let query = $('#filter_str').val().toLowerCase();
            let selector = (config.changes_bar.mode == 1 ? '#filterPrepend .changesBarLine' : '#changesbar .changesBarLine');
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
        for (let h in this.hosts) {
            hosts.push([h, this.hosts[h].currentStatus])
        }

        let params = new URLSearchParams();
        params.append('key', config.secret_key);
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
                    if (hostName in this.hosts) {
                        this.updateChangesBar(this.hosts[hostName], hostName, data);
                        this.hosts[hostName].updateStatus(data.status);
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
        axios.get("https://raw.githubusercontent.com/jocafamaka/nagmapReborn/developing/VERSION")
            .then(response => {
                if (config.ngr_version != null && (config.ngr_version != response.data)) {
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
        if (config.default_auth) {
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
                    _paq.push(["setDocumentTitle", document.domain + "/" + document.title]); _paq.push(["setCustomVariable", 1, "versao", config.ngr_version, "visit"]); _paq.push(["trackPageView"]); _paq.push(["enableLinkTracking"]); (function () { var u = `https://${config.domain}/analytics/`; _paq.push(["setTrackerUrl", u + "piwik.php"]); _paq.push(["setSiteId", "2"]); var d = document, g = d.createElement("script"), s = d.getElementsByTagName("script")[0]; g.type = "text/javascript"; g.async = true; g.defer = true; g.src = u + "piwik.js"; s.parentNode.insertBefore(g, s); })();
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