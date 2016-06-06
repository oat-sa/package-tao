define([
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/interactions/portableCustomInteraction/main',
    'taoQtiItem/qtiItem/helper/util',
    'taoQtiItem/qtiXmlRenderer/helper/portableElementTpl'
], function(tpl, util){

    return {
        qtiClass : 'customInteraction',
        template : tpl,
        getData : function(interaction, data){
            var markupNs = interaction.getMarkupNamespace();
            data.markup = util.addMarkupNamespace(interaction.markup, markupNs ? markupNs.name : '');
            return data;
        }
    };
});