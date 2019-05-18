/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

// Handler the cover states
coverHanlder = () => {
    if (window.generalStatus >= 0) {

        // Case successfully loaded, display the map
        if (window.generalStatus === 1) {
            tp.stop();
            $("#cover").addClass("fadeOut fast");
            setTimeout(() => {
                $("#cover").remove()
            }, 500);

            swal({
                type: "warning",
                title: 'Nagmap Reborn v2.0.0',
                html: i18next.t('not_released'),
                footer: `<a href="https://github.com/jocafamaka/nagmapReborn/releases">${i18next.t('last_stable')}</a>`,
                confirmButtonText: 'OK'
            });
        }
        // If it is still loading, wait to check again
        else {
            setTimeout(() => {
                coverHanlder()
            }, 1500);
        }
    }
    // In case of error it displays the error page
    else {

        // Stop the typed
        tp.stop();

        // Stop the marker animation
        $("#marker_circle").css("animation-iteration-count", 0).css("fill", "#663333");

        // Hide some elements from the page
        $("#marker_pin").fadeOut(200);
        $("#marker_shadow").fadeOut(200);
        $("#cover_msg").hide();

        // Displays error elements
        $("#cover_error").fadeIn(200);
        $("#cover_msg_error").fadeIn(200);
        $("#marker_pin_error").fadeIn(200);
        $("#marker_shadow_error").fadeIn(200);

        // coverMsgUp('cover_error', true);
        if (window.generalStatus === -1)
            $("#cover_msg_error").html(i18next.t('cover_error'));

        if (window.generalStatus === -2)
            $("#cover_msg_error").text(i18next.t('too_long'));

        swal({
            type: "warning",
            title: 'Nagmap Reborn v2.0.0',
            html: i18next.t('not_released'),
            footer: `<a href="https://github.com/jocafamaka/nagmapReborn/releases">${i18next.t('last_stable')}</a>`,
            confirmButtonText: 'OK'
        });
    }
}

/* coverMsgUp = (m, uni = false) => {
    if (uni) {
        $("#cover_msg_error").text(i18next.t(m));
    }
    else {
        if (!window.firstTime)
            $("#cover_msg_error").text(i18next.t('wait', { t: `${i18next.t(m)}...` }));
    }
}
 */

$(document).ready(() => {
    try {

        // Open hosts infoWindow
        openPopup = host => {
            host.bindPopup(host.options.popupContent);
            host.openPopup();
            if (config.cbFilter) {
                tippy('.filter', {
                    arrow: true,
                    followCursor: "horizontal"
                });
            }
            host.unbindPopup();
        }

        i18next.init(i18nConfig).then(function (t) {
            tp = new Typed("#cover_msg", {
                strings: [i18next.t('wait', {
                    t: `${i18next.t('load')}...`
                }), i18next.t('wait', {
                    t: `${i18next.t('cr_map')}...`
                }), i18next.t('wait', {
                    t: `${i18next.t('cr_hosts')}...`
                }), i18next.t('wait', {
                    t: `${i18next.t('cr_markers')}...`
                }), i18next.t('wait', {
                    t: `${i18next.t('cr_lines')}...`
                })],
                typeSpeed: 35,
                startDelay: 2000,
                backSpeed: 40,
                backDelay: 2000,
                loop: true,
                showCursor: true,
                cursorChar: " ",
            });
            setTimeout(() => {
                coverHanlder();
                tooLong = setTimeout(() => {
                    window.generalStatus = -2
                }, 50000);
            }, 1950);
            /* window.firstTime = false;
            $("#cover_msg_error").text(i18next.t('start'));
            coverMsgUp('start', true); */
            window.nagmapReborn = new NagmapReborn(config);
        });
    } catch (e) {
        Utils.initErrorHandler(e);
    }
});