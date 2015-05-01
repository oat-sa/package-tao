define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/img', 'taoQtiItem/qtiCommonRenderer/helpers/container'], function(tpl, containerHelper){
    return {
        qtiClass : 'img',
        template : tpl,
        getContainer : containerHelper.get
    };
});
