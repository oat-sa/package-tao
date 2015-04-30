define([
    'lodash',
    'taoQtiItem/qtiCreator/helper/qtiElements',
    'taoQtiItem/qtiCreator/editor/customElementRegistryFactory'
], function(_, qtiElements, factory){
    
    "use strict";
    
    var registry = factory.create({
        onRegister : function(hook){
            //for compatiblility
            qtiElements.classes['customInteraction.' + hook.typeIdentifier] = {parents : ['customInteraction'], qti : true};
        }
    });

    /**
     * Get authorign data for a custom interaction
     * 
     * @param {String} typeIdentifier
     * @returns {Object}
     */
    registry.getAuthoringData = function(typeIdentifier){

        var manifest = registry.getManifest(typeIdentifier);

        return {
            label : manifest.label, //currently no translation available 
            icon : registry.getBaseUrl(typeIdentifier) + manifest.icon, //use baseUrl from context
            short : manifest.short,
            description : manifest.description,
            qtiClass : 'customInteraction.' + manifest.typeIdentifier, //custom interaction is block type
            tags : _.union(['Custom Interactions'], manifest.tags)
        };
    }

    return registry;
});