define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/inlineChoice', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'inlineChoice',
        getContainer : Helper.getContainer,
        template : tpl
    };
});