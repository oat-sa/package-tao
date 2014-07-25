define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/gapImg', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'gapImg',
        getContainer : Helper.getContainer,
        template : tpl
    };
});
