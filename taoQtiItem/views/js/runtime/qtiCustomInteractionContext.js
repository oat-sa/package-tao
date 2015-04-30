define(function(){

    //need a global reference to have pciHooks shared in two different requirejs context ("default" and "portableCustomInteraction")
    window._pciHooks = window._pciHooks || {};

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
         * @param {Object} pciHook
         * @returns {undefined}
         */
        register : function(pciHook){
            //@todo check pciHook validity
            window._pciHooks[pciHook.getTypeIdentifier()] = pciHook;
        },
        /**
         * notify when a custom interaction is ready for test taker interaction
         * 
         * @param {string} pciInstance
         * @fires custominteractionready
         */
        notifyReady : function(pciInstance){
            //@todo add pciIntance as event data and notify event to delivery engine
        },
        /**
         * notify when a custom interaction is completed by test taker
         * 
         * @param {string} pciInstance
         * @fires custominteractiondone
         */
        notifyDone : function(pciInstance){
            //@todo add pciIntance as event data and notify event to delivery engine
        },
        /**
         * Get a cloned object representing the PCI model
         * 
         * @param {string} pciTypeIdentifier
         * @returns {Object} clonedPciModel
         */
        createPciInstance : function(pciTypeIdentifier){

            if(window._pciHooks[pciTypeIdentifier]){

                var instance = {},
                    proto = window._pciHooks[pciTypeIdentifier];

                for(var name in proto){
                    if(typeof proto[name] === 'function'){
                        //@todo : delegate function call for better performance ?
                        instance[name] = proto[name];
                    }else if(proto[name] !== null && typeof proto[name] === 'object'){
                        //a plain object:
                        instance[name] = proto[name].constructor();
                    }else{
                        //not an object (nor a function) : e.g. 0, 123, '123', null, undefined
                        instance[name] = proto[name];
                    }
                }

                return instance;

            }else{
                throw 'no portable custom interaction hook found with the id ' + pciTypeIdentifier;
            }
        }
    };
    
    
    return taoQtiCustomInteractionContext;
});