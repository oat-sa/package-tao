define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/choice', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'simpleChoice.orderInteraction',
        getContainer : Helper.getContainer,
        template : tpl
    };
});