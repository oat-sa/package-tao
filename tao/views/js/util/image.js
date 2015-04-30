/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(function(){
    'use strict';

    /**
     * Image manipulation utility library
     * @exports image 
     */
    return {

        /**
         * Get the size of an image before displaying it.
         * @param {String} src - the image source url
         * @param {Number} [timeout = 2] - image load timeout in secs
         * @param {ImageSizeCallback} cb - called with the image size
         */
        getSize : function(src, timeout, cb){
            var timeoutId;
            var img = document.createElement('img');

            //params interchange
            if(typeof(timeout) === 'function'){
                cb = timeout;
                timeout = 2;
            }

            img.onload = function(){
                if(timeoutId){
                    clearTimeout(timeoutId);

                    /**
                     * @callback ImageSizeCallback
                     * @param {Object|Null} [size] - null if the image can't be loaded
                     * @param {Number} size.width
                     * @param {Number} size.height
                     */ 
                    cb({
                        width   : img.naturalWidth || img.width,
                        height  : img.naturalHeight || img.height
                    });
                }
            };    
            img.onerror = function(){
                if(timeoutId){
                    clearTimeout(timeoutId);
                    cb(null);
                }
            };
            timeoutId = setTimeout(function(){
                cb(null);
            }, timeout * 1000);
            img.src = src;
        }
    };
});
