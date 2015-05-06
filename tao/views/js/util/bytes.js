/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(function(){
    'use strict';

    /**
     * Util object to manipulate bytes
     * @exports util/bytes
     */
    var bytesUtil = {

        /**
         * Get Human Readable Size
         * @param {Number} bytes - the number of bytes
         * @returns {String} the size converted
         */
        hrSize : function hrSize(bytes) {
            
            var units = ['B', 'kB','MB','GB','TB'];
            var unit = 0;
            var thresh = 1024; 
            bytes = bytes || 0;
            while(bytes >=thresh) {
                bytes /= thresh;
                unit++;
            }
            return bytes.toFixed(2)  + units[unit];
        }
    };

    return bytesUtil;
});
