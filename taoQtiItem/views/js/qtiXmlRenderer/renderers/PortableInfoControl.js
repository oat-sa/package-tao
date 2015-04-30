define([
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/portableInfoControl',
    'taoQtiItem/qtiItem/helper/util',
    'taoQtiItem/qtiXmlRenderer/helper/portableElementTpl'
], function(tpl, util){

    return {
        qtiClass : 'infoControl',
        template : tpl,
        getData : function(infoControl, data){
            var markupNs = infoControl.getMarkupNamespace();
            data.markup = util.addMarkupNamespace(infoControl.markup, markupNs ? markupNs.name : '');
            return data;
        }
    };
});