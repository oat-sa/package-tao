define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/prompt', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'prompt',
        template : tpl,
        getContainer : Helper.getContainer
    };
});