define([
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/object',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiDefaultRenderer/widgets/Object'
], function(tpl, Helper, DefaultRendererObject){
    return {
        qtiClass : 'object',
        template : tpl,
        getContainer : Helper.getContainer,
        render : function(obj){
            var media = new DefaultRendererObject(obj);
            media.render();
        }
    };
});