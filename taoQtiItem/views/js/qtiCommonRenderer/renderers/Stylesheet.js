define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/stylesheet', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'stylesheet',
        template : tpl,
        getContainer : Helper.getContainer
    };
});