/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

window.generalStatus = STATUS.GENERAL.initial;

_u = function consoleDebug(msg, ok = true) {
    if (config.debug) {
        ok ? console.log(msg) : console.warn(msg);
        $("#console_text").prepend($('<p>', {
            html: new Date().toLocaleString(config.locale) + ' - ' + String(msg).replace(/{/g, '<b>').replace(/}/g, '</b>'),
            class: (ok) ? 'debugText ok' : 'debugText error'
        }));
    }
}

axios.get(`initializer.php?${Utils.getFullQueryString()}`)
    .then(function (response) {
        window.config = response.data;
        i18nConfig = {
            lng: config.locale,
            debug: config.debug,
            resources: {
                [`${config.locale}`]: {
                    translation: config.translation
                }
            }
        };

        init();
    })
    .catch(function (error) {
        Utils.initErrorHandler(error.response.data.error || error);
        if (error.response.status == 401)
            window.generalStatus = STATUS.GENERAL.accessDenied;
        coverHanlder();
    });

// Check compatibility with ES6
try {
    (eval("let foo = () => {};"));
} catch (e) {
    window.generalStatus = STATUS.GENERAL.incompatible;
    coverHanlder();
}

// Handler the cover animation
function animateBack() {

    setTimeout(function () {
        if (window.generalStatus != STATUS.GENERAL.finished) {

            $("#cover_error").css("background", "radial-gradient(circle, rgb(236, 91, 91) " + firstRadial + "%, rgb(204, 29, 29) " + secondRadial + "%, rgb(175, 0, 0) 100%)");
            $("#cover").css("background", "radial-gradient(circle, rgb(7, 194, 188) " + firstRadial + "%, rgb(0, 168, 163) " + secondRadial + "%, rgb(2, 124, 120) 100%)");
            if (direction == "up") {
                firstRadial = firstRadial + .5;
                secondRadial = secondRadial - .7;
            } else {
                firstRadial = firstRadial - .5;
                secondRadial = secondRadial + .7;
            }
            if (firstRadial == 13) {
                direction = "down";
            }
            if (firstRadial == 1) {
                direction = "up";
            }
            animateBack();
        }
    }, 70);
};
animateBack();

// Handler the cover states
function coverHanlder() {
    if (window.generalStatus >= STATUS.GENERAL.initial) {

        // Case successfully loaded, display the map
        if (window.generalStatus === STATUS.GENERAL.finished) {
            tp.stop();
            $("#cover").addClass("fadeOut fast");
            setTimeout(function () {
                $("#cover").remove()
            }, 500);

            Swal.fire({
                heightAuto: false,
                icon: "warning",
                title: 'Nagmap Reborn v2.0.0-rc2',
                html: i18next.t('not_released'),
                footer: `<a href="https://github.com/jocafamaka/nagmapReborn/releases">${i18next.t('last_stable')}</a>`,
                confirmButtonText: 'OK'
            });

            window.nagmapReborn.checkNgRebornUpdate();

            _u("Showing map");
        }
        // If it is still loading, wait to check again
        else {
            setTimeout(function () {
                coverHanlder()
            }, 1500);
        }
    }
    // In case of error it displays the error page
    else {
        // Stop the typed
        if (tp)
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
        $("#error_button").fadeIn(200).addClass("animated shake delay-05s");

        // coverMsgUp('cover_error', true);
        if (window.generalStatus === STATUS.GENERAL.accessDenied)
            $("#cover_msg_error").html("Access denied!");

        if (window.generalStatus === STATUS.GENERAL.generealError)
            $("#cover_msg_error").html(i18next.t('cover_error') || 'An error has occurred!');

        if (window.generalStatus === STATUS.GENERAL.tooLong) {
            Utils.initErrorHandler(i18next.t('too_long_details'));
            $("#cover_msg_error").text(i18next.t('too_long'));
        }

        if (window.generalStatus === STATUS.GENERAL.incompatible) {
            $("#cover_msg_error").html((i18next.t('unsupported_browser') || "<i class='material-icons' style='font-size: 5vh;'>warning</i> this browser is not compatible with NagmapReborn, please upgrade your browser or use a more modern one!"));
            $("#error_button").css("display", "none");
        }

        _u("Showing error cover");

        Swal.fire({
            heightAuto: false,
            icon: "warning",
            title: 'Nagmap Reborn v2.0.0-rc2',
            html: i18next.t('not_released'),
            footer: `<a href="https://github.com/jocafamaka/nagmapReborn/releases">${i18next.t('last_stable') || 'Get the last stable version'}</a>`,
            confirmButtonText: 'OK'
        });
    }
}

function init() {
    try {

        // Open hosts infoWindow
        openPopup = function (marker) {
            var marker = (typeof marker == 'string') ? nagmapReborn.hosts[marker].marker : marker;
            marker.bindPopup(marker.options.popupContent);
            marker.openPopup();

            tippy('.filter', {
                arrow: true,
                interactive: true
            });

            tippy('.address', {
                arrow: true,
                placement: 'bottom',
                interactive: true
            });

            marker.unbindPopup();
        }

        _u("Starting translation library.");

        i18next.init(i18nConfig).then(function (t) {
            _u("Displaying loading message.");

            jqueryI18next.init(i18next, $, {
                tName: 't',
                i18nName: 'i18n',
                handleName: 'localize',
                selectorAttr: 'data-i18n',
                targetAttr: 'i18n-target',
                optionsAttr: 'i18n-options',
                useOptionsAttr: false,
                parseDefaultValueFromContent: true
            });

            $(document).localize();

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
                startDelay: 1800,
                backSpeed: 40,
                backDelay: 2000,
                loop: true,
                showCursor: true,
                cursorChar: " ",
            });
            setTimeout(function () {
                coverHanlder();
                tooLong = setTimeout(function () {
                    window.generalStatus = STATUS.GENERAL.tooLong
                }, 50000);
            }, 1950);

            _u("Initializing Nagmap Reborn class.");

            window.nagmapReborn = new NagmapReborn();
        });

    } catch (e) {
        Utils.initErrorHandler(e);
    }
}