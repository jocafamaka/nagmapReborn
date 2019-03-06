/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

class Host {
	constructor(data, icons, oms) {
		this.latlng = new L.latLng((data.latlng).split(","));
		this.currentStatus = data.status;
		this.mark = this.createMark(data, icons, oms);
		this.parents = data.parents;
		this.lines = [];
	}

	/*
	 * Responsible for creating the host marker and infoWindow. 
	 */
	createMark(data, icons, oms) {

		let icon = icons.grey;
		let zIndex = 2000;

		if (this.currentStatus === 0) {
			icon = icons.green;
			zIndex = 2000;
		} else if (this.currentStatus === 1) {
			icon = icons.yellow;
			zIndex = 3000;
		} else if (this.currentStatus === 2) {
			icon = icons.orange;
			zIndex = 4000;
		} else if (this.currentStatus === 3) {
			icon = icons.red;
			zIndex = 5000;
		}

		let hostgroups = "";
		let parents = "";

		data.hostgroups.forEach(e => {
			hostgroups += `${e}<br>`;
		});

		if (data.parents) {
			data.parents.forEach(e => {
				parents += `${e}<br>`;
			});
		}

		let marker = L.marker(this.latlng, {
			icon: icon,
			title: data.nagios_host_name,
			zIndexOffset: zIndex,
			popupContent: `
				<div class="bubble">
					<h3><strong>${data.nagios_host_name}</strong></h3>
					
					<table>
						<tr ${(config.cbFilter && config.cbMode) ? `class="filter" data-tippy-content="${i18next.t('as_filter')}"` : ""} ><td><strong>${i18next.t('alias')}</strong></td><td>:</td><td>${data.alias}</td></tr>
						<tr><td><strong>${i18next.t('hostG')}</strong></td><td>:</td><td>${hostgroups}</td></tr>
						<tr><td><strong>${i18next.t('address')}</strong></td><td>:</td><td>${data.address}</td></tr>
						<tr><td><strong>${i18next.t('parent')}</strong></td><td>:</td><td>${parents}</td></tr>
					</table>
					<center>
						<br>
						<a href="https://www.github.com/jocafamaka/nagmapReborn/">
							<img title="${i18next.t('project')}" src="resources/img/logoMiniBlack.png" alt="">
						</a>
					<center>
				</div>
			`
		}).addTo(window.map);

		oms.addMarker(marker);

		return marker;
	}
}