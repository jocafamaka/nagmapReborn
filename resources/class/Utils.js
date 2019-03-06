/******************************************************************************************
 * 
 * Developed by: João Ribeiro - Nagmap Reborn (https://github.com/jocafamaka/nagmapReborn)
 * 
 ******************************************************************************************/

/* Setting initial parameters. */
var generalStatus = 0;
var msgError = "";
var tp = null;
var tooLong = null;


class Utils {
    /* Global errors handler */
    static initErrorHandler = (error = null, msg = "") => {
        generalStatus = -1;
        msgError = msg;
        //alert(error.message);
    }

}