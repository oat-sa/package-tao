define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/Object'
], function(_, editable, Object){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                data : '',
                type : ''
            };
        }
    });
    return Object.extend(methods);
});