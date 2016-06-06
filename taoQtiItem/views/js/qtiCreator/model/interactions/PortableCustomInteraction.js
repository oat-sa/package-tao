define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiCreator/model/helper/portableElement',
    'taoQtiItem/qtiCreator/editor/customInteractionRegistry',
    'taoQtiItem/qtiItem/core/interactions/CustomInteraction'
], function(_, editable, editableInteraction, portableElement, ciRegistry, Interaction){
    "use strict";
    var _throwMissingImplementationError = function(pci, fnName){
        throw fnName + ' not available for pci of type ' + pci.typeIdentifier;
    };

    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, portableElement.getDefaultMethods(ciRegistry));
    _.extend(methods, {
        createChoice : function(){

            var creator = ciRegistry.getCreator(this.typeIdentifier);
            if(_.isFunction(creator.createChoice)){
                return creator.createChoice(this);
            }else{
                _throwMissingImplementationError(this, 'createChoice');
            }
        },
        getDefaultMarkupTemplateData : function(){
            return {
                responseIdentifier : this.attr('responseIdentifier')
            };
        }
    });

    return Interaction.extend(methods);
});