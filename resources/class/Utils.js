/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

// Setting initial parameters.
var generalStatus = 0;
var msgError = "";
var tp = null;
var tooLong = null;

class Utils {

    // Global errors handler
    static initErrorHandler(error = null, msg = "") {
        generalStatus = -1;
        // msgError = msg;
        // console.log(error);

        $("#modal_error").html(`
        <div class="modal-content">
            <h5>${msg || i18next.t('cover_error') || 'General fail'}</h5>
            <br>
            <strong>${i18next.t('details')}:</strong> ${error}
            <br>
            <br>
            <strong>Stack:</strong> 
            ${error.stack}
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-light btn-flat">${i18next.t('close')}</a>
        </div>
        `);
    }

}