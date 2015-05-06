define([
    'lodash', 
    'taoQtiItem/qtiCreator/model/mixin/editable', 
    'taoQtiItem/qtiItem/core/choices/Gap'
], function(_, editable, Gap){
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
               required : false
            };
        }
    });
    return Gap.extend(methods);
});


