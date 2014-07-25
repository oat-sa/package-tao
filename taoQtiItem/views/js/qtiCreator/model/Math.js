define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/Math'
], function(_, editable, Math){
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
    return Math.extend(methods);
});