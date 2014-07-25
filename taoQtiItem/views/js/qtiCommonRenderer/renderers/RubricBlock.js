define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/rubricBlock', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'rubricBlock',
        getContainer : Helper.getContainer,
        template : tpl
    };
});