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
						<tr ${(config.cbFilter && config.cbMode) ? `class="filter" data-tippy-content="${i18next.t('as_filter')}"` : ""} ><td><strong>${i18next.t('alias')}</strong></td><td>:</td><td>${data.alias}</td></tr>
						<tr><td><strong>${i18next.t('hostG')}</strong></td><td>:</td><td>${hostgroups}</td></tr>
						<tr class="address" data-tippy-content="<a class='address-link' target='_blank' href='http://${data.address}'>http <img src='${base64White}' alt='Link' ></a> | <a class='address-link' target='_blank' href='https://${data.address}'>https <img src='${base64White}' alt='Link' /></a></strong>"><td><strong>${i18next.t('address')}</strong></td><td>:</td><td><i>${data.address}</i> <img src='${base64Black}' alt='Link' /></td></tr>
						<tr><td><strong>${i18next.t('parent')}</strong></td><td>:</td><td>${parents}</td></tr>
						<tr><td colspan=3 style="text-align: center"><br><a target="_blank" href="https://www.github.com/jocafamaka/nagmapReborn/"><img title="${i18next.t('project')}" src="resources/img/logoMiniBlack.png" alt=""></a></td></tr>
					</table>
				</div>
			`
		}).addTo(window.map);

		oms.addMarker(marker);

		return marker;
	}
}