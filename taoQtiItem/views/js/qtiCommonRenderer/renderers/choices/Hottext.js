define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/hottext', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'hottext',
        getContainer : Helper.getContainer,
        template : tpl
    };
});