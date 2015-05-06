define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/choice', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'gapText',
        getContainer : Helper.getContainer,
        template : tpl
    };
}); 