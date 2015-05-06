define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/gap', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'gap',
        getContainer : Helper.getContainer,
        template : tpl
    };
});