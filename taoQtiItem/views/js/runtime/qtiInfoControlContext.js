define(function(){

    //need a global reference to have picHooks shared in two different requirejs context ("default" and "portableCustomInteraction")
    window._picHooks = window._picHooks || {};

    /**
     * Global object accessible by all PICs
     *
     * @type Object
     */
    var taoQtiInfoControlContext = {
        /**
         * register a info control (its runtime model) in global registery
         * register a renderer
         *
         * @param {Object} picHook
         * @returns {undefined}
         */
        register : function(picHook){
            //@todo check picHook validity
            window._picHooks[picHook.getTypeIdentifier()] = picHook;
        },
        /**
         * notify when a info control is ready for test taker interaction
         *
         * @param {string} picInstance
         * @fires custominteractionready
         */
        notifyReady : function(picInstance){
            //@todo add pciInstance as event data and notify event to delivery engine
        },
        /**
         * Get a cloned object representing the PIC model
         *
         * @param {string} typeIdentifier
         * @returns {Object} clonedPciModel
         */
        createPciInstance : function(typeIdentifier){

            if(window._picHooks[typeIdentifier]){

                var instance = {},
                    proto = window._picHooks[typeIdentifier];

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
                throw 'no portable info control hook found with the id ' + typeIdentifier;
            }
        }
    };


    return taoQtiInfoControlContext;
});
