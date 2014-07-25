define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/simpleAssociableChoice.matchInteraction', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'simpleAssociableChoice.matchInteraction',
        getContainer : Helper.getContainer,
        template : tpl
    };
});