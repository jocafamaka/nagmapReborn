/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

class Utils {

    /**
     * Responsible for handler init errors
     * @return undefined
     */
    static initErrorHandler(error = null, msg = "") {
        generalStatus = -1;

        if (Array.isArray(error)) {
            var errors = '';
            error.forEach(function (e) {
                errors += `${e}<br>`
            });

            $("#modal_error").html(`
            <div class="modal-content">
                <h5>Config error</h5>
                <br>
                <strong>${errors}
                <br>
                <br>
                <strong>Stack:</strong> 
                ${error.stack || '-'}
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-light btn-flat">Close</a>
            </div>
            `);

        }
        else {
            $("#modal_error").html(`
            <div class="modal-content">
                <h5>${msg || i18next.t('cover_error') || 'General fail'}</h5>
                <br>
                <strong>${i18next.t('details') || 'Details'}:</strong> ${error || '-'}
                <br>
                <br>
                <strong>Stack:</strong> 
                ${error.stack || '-'}
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-light btn-flat">${i18next.t('close') || 'Close'}</a>
            </div>
            `);

            _u(error, false);
        }

    }

    /**
     * Get locale formated date. 
     * @return Date date
     */
    static now() {
        var date = new Date();
        return date.toLocaleString(config.locale);
    }

    /**
     * Show a toastr. 
     * @return undefined
     */
    static showToastr(type, msg) {

        Swal.fire({
            icon: type,
            title: msg,
            toast: true,
            position: (config.changes_bar.mode == 3) ? 'top-start' : 'top-end',
            showConfirmButton: false,
            timer: (config.update_time * 1000),
            timerProgressBar: true,
            customClass: {
                popup: 'sw2Custom'
            },
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }

    static getFullQueryString() {
        var p = window.location.href.split("?");
        return p[1] || '';
    }

}