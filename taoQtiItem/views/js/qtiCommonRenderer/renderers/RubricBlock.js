define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/rubricBlock', 'taoQtiItem/qtiCommonRenderer/helpers/container'], function(tpl, containerHelper){
    return {
        qtiClass : 'rubricBlock',
        getContainer : containerHelper.get,
        template : tpl
    };
});
