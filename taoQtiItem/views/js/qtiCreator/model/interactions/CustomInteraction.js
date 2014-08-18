define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableInteraction',
    'taoQtiItem/qtiItem/core/interactions/CustomInteraction',
    'taoQtiItem/qtiCreator/helper/pciCreator'
], function(_, editable, editableInteraction, Interaction, pciCreator){
    
    var _throwMissingImplementationError = function(pci, fnName){
        throw fnName+' not available for pci of type '+pci.typeIdentifier;
    };
    
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableInteraction);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {};
        },
        getDefaultPciProperties : function(){
            
            var pci = pciCreator.getPciInstance(this);
            if(_.isFunction(pci.createChoice)){
                return pci.getDefaultPciProperties(this);
            }else{
                return {};
            }
        },
        afterCreate : function(){
            
            var pci = pciCreator.getPciInstance(this);
            if(_.isFunction(pci.createChoice)){
                return pci.afterCreate(this);
            }
        },
        createChoice : function(){
        
            var pci = pciCreator.getPciInstance(this);
            if(_.isFunction(pci.createChoice)){
                return pci.createChoice(this);
            }else{
                _throwMissingImplementationError(this, 'createChoice');
            }
        }
    });
    return Interaction.extend(methods);
});