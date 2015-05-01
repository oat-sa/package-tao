define(['IMSGlobal/jquery_2_1_1', 'qtiInfoControlContext'], function($, qtiInfoControlContext){

    var studentToolCalculator = {
        id : -1,
        getTypeIdentifier : function(){
            return 'studentToolCalculator';
        },
        /**
         * Initialize the PIC
         * 
         * @param {String} id
         * @param {Node} dom
         * @param {Object} config - json
         */
        initialize : function(id, dom, config){

            this.id = id;
            this.dom = dom;
            this.config = config || {};

            var $container = $(dom);
            
            console.log('init', this.getTypeIdentifier());
        },
        /**
         * Reverse operation performed by render()
         * After this function is executed, only the inital naked markup remains 
         * Event listeners are removed and the state and the response are reset
         * 
         * @param {Object} interaction
         */
        destroy : function(){

            var $container = $(this.dom);
            $container.off().empty();
        },
        /**
         * Restore the state of the interaction from the serializedState.
         * 
         * @param {Object} interaction
         * @param {Object} serializedState - json format
         */
        setSerializedState : function(state){

            console.log('state set to', state);
        },
        /**
         * Get the current state of the interaction as a string.
         * It enables saving the state for later usage.
         * 
         * @param {Object} interaction
         * @returns {Object} json format
         */
        getSerializedState : function(){

            return {};
        }
    };

    qtiInfoControlContext.register(studentToolCalculator);
});