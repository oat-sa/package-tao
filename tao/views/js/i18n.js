define(['lodash', 'json!i18ntr/messages.json', 'context', 'core/format'], function(_, i18nTr, context, format){
    'use strict';   
 
    var translations = i18nTr.translations;
    
    /**
     * Common translation method.
     * @see /locales/#lang#/messages_po.js
     * 
     * @param {String} message should be the string in the default language (usually english) used as the key in the gettext translations  
     * @returns {String} translated message 
     */
    var __ = function __(message){
        var localized =  !translations[message] ? message :  translations[message];

        if(arguments.length > 1){
            arguments[0] = localized;
            localized = format.apply(null, arguments); 
        }

        return localized;
    };

    //expose the translation function
    return __ ;
});
