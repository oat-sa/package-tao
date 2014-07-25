define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/img', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'img',
        template : tpl,
        getContainer : Helper.getContainer
    };
});