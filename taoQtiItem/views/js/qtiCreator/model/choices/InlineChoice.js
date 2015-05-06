define(['lodash', 'taoQtiItem/qtiCreator/model/mixin/editable', 'taoQtiItem/qtiItem/core/choices/InlineChoice'], function(_, editable, Choice){
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                'fixed' : false,
                'showHide' : 'show'
            };
        }
    });
    return Choice.extend(methods);
});