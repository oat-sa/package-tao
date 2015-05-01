define([
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/object',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiDefaultRenderer/widgets/Object'
], function(tpl, containerHelper, DefaultRendererObject){
    return {
        qtiClass : 'object',
        template : tpl,
        getContainer : containerHelper.get,
        render : function(obj){
            var media = new DefaultRendererObject(obj);
            media.render();
        }
    };
});
