define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/Include'
], function(_, editable, Include){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {};
        },
        afterCreate : function(){
            this.getNamespace();
        }
    });
    return Include.extend(methods);
});