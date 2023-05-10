/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

class Host {
	constructor(h, data, oms) {
		this._u(`Creating host {${h}} at {${data.latlng}}.`);
		this.alias = data.alias;
		this.hostName = data.host_name;
		this.latlng = new L.latLng((data.latlng).split(","));
		this.currentStatus = data.status;
		this.parents = data.parents;
		this.hostgroups = data.hostgroups;
		this.iconStyle = this.getIconStyle();
		this.marker = this.createMarker(data, oms);
		this.lines = [];
	}


	/**
	 * Responsible for creating the host marker and infoWindow. 
	 * @return Marker mark
	 */
	createMarker(data, oms) {
		let icon = config.icons.styles[this.iconStyle].grey;
		let zIndex = (config.priorities.unknown * 1000);

		if (this.currentStatus === STATUS.HOSTS.up) {
			icon = config.icons.styles[this.iconStyle].green;
			zIndex = (config.priorities.up * 1000);
		} else if (this.currentStatus === STATUS.HOSTS.warning) {
			icon = config.icons.styles[this.iconStyle].yellow;
			zIndex = (config.priorities.warning * 1000);
		} else if (this.currentStatus === STATUS.HOSTS.critical) {
			icon = config.icons.styles[this.iconStyle].orange;
			zIndex = (config.priorities.critical * 1000);
		} else if (this.currentStatus === STATUS.HOSTS.down) {
			icon = config.icons.styles[this.iconStyle].red;
			zIndex = (config.priorities.down * 1000);
		}

		let hostgroups = "";
		let parents = "";

		if (data.hostgroups)
			data.hostgroups.forEach(e => { hostgroups += `${e}<br>`; });

		if (data.parents) {
			data.parents.forEach(e => {
				parents += `${e}<br>`;
			});
		}

		let base64Black = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAQAAAD8fJRsAAAAAmJLR0QA/4ePzL8AAACPSURBVBjThc8xDgFhFEXhc/4wJlHZhFIrEr1SpVFYl16nQohgOxIS7IB5CsNEFO6pXr7qATRpldXS3DAMj6Spj/IIenSZeDUMvHtO81e0yd0bngwMD7zXcGu4YPwNmWvDFRkddxVkLg03NOgzIlUwMNySgxuDrAIZksMvfPYXHt48fHWxoE6aWrw/LyvSDJ6Qq0l9CGPrYQAAAABJRU5ErkJggg==";
		let base64White = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAQAAAD8fJRsAAAAAmJLR0QA/4ePzL8AAAB/SURBVBjThckxDsEAGIDRn1BNTC5htIrEbjRZDM5lt5kQIvQ6EhLcgD6D0jQG3ze+iNDWKW5YencKc0+fBvpmbhAeLpbFXakjzgSyKNKyx8q0AhJbbCR6Dl+QWGOnZWiiXsIIe2mEHZISasbSiB8o+wtPd1nlq1wzzOWq5RYRLwFVuLSYFSaaAAAAAElFTkSuQmCC";

		let marker = L.marker(this.latlng, {
			icon: icon,
			title: data.nagios_host_name,
			zIndexOffset: zIndex,
			popupContent: `
				<div class="bubble">
					<h5><strong>${data.nagios_host_name}</strong></h5>
					<table>
						<tr ${(config.changes_bar.filter && config.changes_bar.mode) ? `class="filter" data-tippy-content="${i18next.t('as_filter')}" onclick="$('#filter_str').val($(this).children().next().next().text());M.updateTextFields();nagmapReborn.search();"` : ""}><td><strong>${i18next.t('alias')}</strong></td><td>:</td><td>${data.alias}</td></tr>
						<tr><td><strong>${i18next.t('hostG')}</strong></td><td>:</td><td>${hostgroups}</td></tr>
						<tr class="address" data-tippy-content="<a class='address-link' target='_blank' href='http://${data.address}'>http <img src='${base64White}' alt='Link' ></a> | <a class='address-link' target='_blank' href='https://${data.address}'>https <img src='${base64White}' alt='Link' /></a></strong>"><td><strong>${i18next.t('address')}</strong></td><td>:</td><td><i>${data.address}</i> <img src='${base64Black}' alt='Link' /></td></tr>
						<tr><td><strong>${i18next.t('parent')}</strong></td><td>:</td><td>${parents}</td></tr>
						<tr><td colspan=3 style="text-align: center"><br><a target="_blank" href="https://www.github.com/jocafamaka/nagmapReborn/"><img width="100%" style="max-width:250px" title="${i18next.t('project')}" src="resources/img/logoBlack.png" alt=""></a></td></tr>
					</table>
				</div>
			`
		}).addTo(window.map);

		oms.addMarker(marker);

		return marker;
	}

	/**
	 * Responsible for update a host icon and lines. 
	 * @return undefined
	 */
	updateStatus(newStatus) {
		if (this.currentStatus != newStatus) {
			_u(`Update {${this.alias}} to status {${newStatus}}`);

			let time = 1;
			let color = STATUS.COLORS.unknown;
			let zIndex = config.priorities.unknown;
			let icon = config.icons.styles[this.iconStyle].grey;

			if (newStatus === STATUS.HOSTS.up) {
				icon = config.icons.styles[this.iconStyle].green;
				zIndex = config.priorities.up;
				color = STATUS.COLORS.up;
			} else if (newStatus === STATUS.HOSTS.warning) {
				icon = config.icons.styles[this.iconStyle].yellow;
				zIndex = config.priorities.warning;
				color = STATUS.COLORS.warning;
			} else if (newStatus === STATUS.HOSTS.critical) {
				icon = config.icons.styles[this.iconStyle].orange;
				zIndex = config.priorities.critical;
				color = STATUS.COLORS.critical;
			} else if (newStatus === STATUS.HOSTS.down) {
				icon = config.icons.styles[this.iconStyle].red;
				zIndex = config.priorities.down;
				color = STATUS.COLORS.down;
				time = 20;
				if (config.sound_alert)
					config.alertSound.play();
			}

			this.currentStatus = newStatus;
			this.marker.setIcon(icon);
			this.marker.setZIndexOffset(zIndex * 1000);

			if (typeof this.marker._omsData != 'undefined')
				this.marker._omsData.usualZindex = zIndex * 1000;

			this.lines.forEach(line => {
				line.setStyle({ color: color });
			});

			if (config.update_animation) {
				if (this.marker.isBouncing())
					this.marker.stopBouncing();
				else
					this.marker.bounce(time);
			}
		}
	}

	/**
	 * Define the host icon style
	 * @returns string
	 */
	getIconStyle() {
		let iconStyle = null;

		if (this.hostName in config.icons.names) {
			iconStyle = config.icons.names[this.hostName];
		}

		if (iconStyle == null && this.hostgroups) {
			for (let i = 0; i < this.hostgroups.length; ++i) {
				if (this.hostgroups[i] in config.icons.hostgroups) {
					iconStyle = config.icons.hostgroups[this.hostgroups[i]];
					break;
				}
			}
		}

		if (iconStyle == null) {
			iconStyle = config.defaultIconStyle;
		}

		if (!(iconStyle in config.icons.styles)) {
			_u(i18next.t("load_icon_style_error", { t: iconStyle }), false);

			if (!config.icons.styles.hasOwnProperty(config.defaultIconStyle)) {
				_u(i18next.t("load_icon_style_error", { t: config.defaultIconStyle }), false);
				config.defaultIconStyle = "marker_shadow";
			} else
				iconStyle = config.defaultIconStyle;
		}

		return iconStyle;
	}

	/**
	 * Decorator to debug console. 
	 * @return undefined
	 */
	_u(msg) {
		_u(`(${this.constructor.name}): ${msg}`);
	}
}