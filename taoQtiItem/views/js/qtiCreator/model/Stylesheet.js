define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/Stylesheet'
], function(_, editable, Stylesheet){
    
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                href : 'css/tao-user-styles.css',
                title : '',
                type:'text/css',
                media:'all'
            };
        }
    });

    return Stylesheet.extend(methods);
});