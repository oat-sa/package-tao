define(['jquery', 'lodash'], function($, _){

    var _pciModels = {};
    
    /**
     * Global object accessible by all PCIs
     * 
     * @type Object
     */
    var taoQtiCustomInteractionContext = {
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
         * notify when a custom interaction is ready for test taker interaction
         * 
         * @param {string} pciInstance
         * @fires custominteractionready
         */
        notifyReady : function(pciInstance){
            $(document).trigger('custominteractionready', [pciInstance._taoCustomInteraction]);
        },
        /**
         * notify when a custom interaction is completed by test taker
         * 
         * @param {string} pciInstance
         * @fires custominteractiondone
         */
        notifyDone : function(pciInstance){
            $(document).trigger('custominteractiondone', [pciInstance._taoCustomInteraction]);
        },
        /**
         * Get a cloned object representing the PCI model
         * 
         * @param {string} pciTypeIdentifier
         * @returns {Object} clonedPciModel
         */
        getPci : function(pciTypeIdentifier){
            if(_pciModels[pciTypeIdentifier]){
                return _.cloneDeep(_pciModels[pciTypeIdentifier]);
            }
        }
    };

    return taoQtiCustomInteractionContext;
});