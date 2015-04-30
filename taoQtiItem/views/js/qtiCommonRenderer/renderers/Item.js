define([
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/item',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/itemStylesheetHandler'
], function(tpl, containerHelper, itemStylesheetHandler) {
    return {
        qtiClass: 'assessmentItem',
        template: tpl,
        getContainer: containerHelper.get,
        render: function(item) {
            
            //target blank for all <a>
            containerHelper.targetBlank(containerHelper.get(item));
            
            //add stylesheets
            itemStylesheetHandler.attach(item.stylesheets);
        }
    };
});
