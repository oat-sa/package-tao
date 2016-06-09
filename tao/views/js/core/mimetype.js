/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'lodash', 'json!core/mimetypes.json'], function($, _, mimeTypes){
    'use strict';

    /**
     * Helps you to retrieve file type and categories based on a file mime type
     * @exports core/mimetype
     */
    return {

        /**
         * Gets the MIME type of a resource.
         *
         * @param {String} url - The URL of the resource to get type of
         * @param {Function} [callback] - An optional function called when the response is received.
         *                                This callback must accept 2 arguments:
         *                                the first is the potential error if the request failed,
         *                                the second is the MIME type if the request succeed.
         * @returns {mimetype}
         */
        getResourceType : function getResourceType(url, callback) {
            $.ajax({
                type: "HEAD",
                async: true,
                url: url,
                success: function onSuccess(message, text, jqXHR) {
                    var mime = jqXHR.getResponseHeader('Content-Type');
                    if (callback) {
                        callback(null, mime);
                    }
                },

                error: function onError(jqXHR) {
                    var error = jqXHR.status || 404;
                    if (callback) {
                        callback(error);
                    }
                }
            });
            return this;
        },

        /**
         * Get the type from a mimeType regarding the mimeMapping above
         * @param {Object} file - the file
         * @param {String} [file.mime] - the mime type
         * @param {String} [file.name] - the file name
         * @returns {String} the type
         */
        getFileType : function getFileType(file){
            var type;
            var mime = file.mime;
            var extMatch, ext;

            if(mime){
                //lookup for exact mime
                type = _.findKey(mimeTypes, { mimes : [mime]});

                //then check  with star
                if(!type){
                    type = _.findKey(mimeTypes, { mimes : [mime.replace(/\/.*$/, '/*')]});
                }
            }

            //try by extension
            if(!type){
                extMatch  = file.name.match(/\.([0-9a-z]+)(?:[\?#]|$)/i);
                if(extMatch && extMatch.length > 1){
                    ext = extMatch[1];

                    type = _.findKey(mimeTypes, { extensions : [ext]});
                }
            }

            return type;
        },

        /**
         * Get the category of a type
         * @param {String} type
         * @returns {String} category
         */
        getCategory : function getCategory(type){
            if(mimeTypes[type]){
                return mimeTypes[type].category;
            }
        }

    };

});
