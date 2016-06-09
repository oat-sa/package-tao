define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/RubricBlock'
], function(_, editable, RubricBlock){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                'view' : ['candidate'],
                'use' : ''
            };
        }
    });
    return RubricBlock.extend(methods);
});


