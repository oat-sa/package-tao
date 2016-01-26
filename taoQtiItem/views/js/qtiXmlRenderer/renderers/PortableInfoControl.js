
define([
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/portableInfoControl',
    'taoQtiItem/qtiItem/helper/util',
    'taoQtiItem/qtiXmlRenderer/helper/portableElementTpl'
], function(tpl, util){
    'use strict';

    return {
        qtiClass : 'infoControl',
        template : tpl,
        getData : function(infoControl, data){
            var markupNs = infoControl.getMarkupNamespace();
            data.markup = util.addMarkupNamespace(infoControl.markup, markupNs ? markupNs.name : '');

            //ensure infoControl have an id, otherwise generate one in order to be able to identify it for the state
            if(!infoControl.attr('id')){
                infoControl.attr('id', util.buildId(infoControl.getRelatedItem(), infoControl.typeIdentifier ));
            }

            return data;
        }
    };
});
