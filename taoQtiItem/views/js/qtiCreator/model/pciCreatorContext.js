define(['lodash'], function(_){
    
    "use strict";
    
    var _pciModels = {};
    
    /**
     * Global object accessible by all PCIs
     * 
     * @type Object
     */
    var pciCreatorContext = {
        /**
         * register a custom interaction (its runtime model) in global registery
         * register a renderer
         * 
         * @param {Object} pciModel
         * @returns {undefined}
         */
        register : function(pciModel){
            //@todo check pciModel validity
            _pciModels[pciModel.getTypeIdentifier()] = pciModel;
        },
        /**
         * Get a cloned object representing the PCI model
         * 
         * @param {string} pciTypeIdentifier
         * @returns {Object} clonedPciModel
         */
        createPciInstance : function(pciTypeIdentifier){
            if(_pciModels[pciTypeIdentifier]){
                return _.cloneDeep(_pciModels[pciTypeIdentifier]);
            }
        }
    };

    return pciCreatorContext;
});