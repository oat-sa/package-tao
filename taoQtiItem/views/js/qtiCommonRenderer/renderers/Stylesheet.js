define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/stylesheet', 'taoQtiItem/qtiCommonRenderer/helpers/container'], function(tpl, containerHelper){
    return {
        qtiClass : 'stylesheet',
        template : tpl,
        getContainer : containerHelper.get
    };
});
