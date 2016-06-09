define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/choices/GapText'
], function(_, editable, Choice){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                fixed : false,
                matchMax : 1,
                matchMin : 0
            };
        }
    });
    return Choice.extend(methods);
});


