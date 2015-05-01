define(['jquery'], function($){

    /**
     * The global qtiCustomInteractionContext
     * Global object qtiCustomInteractionContext cannot be passed as AMD dependency because the pci libs are loaded under a different requirejs context.
     * This means that the dependencies of qtiCustomInteractionContext would also need to be compied into this one which is too complicated.
     * 
     * @type {Object} - the globally scoped qtiCustomInteractionContext
     */
    var _pciContext = window.qtiCustomInteractionContext;

    var likertScaleInteraction = {
        id : -1,
        getTypeIdentifier : function(){
            return 'likertScaleInteraction';
        },
        /**
         * Render the PCI : 
         * @param {String} id
         * @param {Node} dom
         * @param {Object} config - json
         */
        initialize : function(id, dom, config){

            this.id = id;
            this.dom = dom;
            this.config = config || {};

            var $container = $(dom),
                $li,
                scale = parseInt(config.scale),
                $ul = $container.find('ul.likert');

            //create scale:
            scale = scale || 5;

            for(var i = 1; i <= scale; i++){

                $li = $('<li>', {'class' : 'likert'});
                $li.append($('<input>', {type : 'radio', name : this.id, value : i}));

                $ul.append($li);
            }

            //add labels:
            var $labelMin = $('<label>', {'class' : 'likert-label likert-label-min'}).html(config['label-min']);
            var $labelMax = $('<label>', {'class' : 'likert-label likert-label-max'}).html(config['label-max']);

            $ul.find('li:first').prepend($labelMin);
            $ul.find('li:last').append($labelMax);

            //tell the rendering engine that I am ready
            _pciContext.notifyReady(this);

            /**
             * Test response change
             */
            var self = this;
            $container.change(function(){
                var r = self.getResponse();
                console.log(r.base.integer);
            });
        },
        /**
         * Programmatically set the response following the json schema described in
         * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * 
         * @param {Object} interaction
         * @param {Object} response
         */
        setResponse : function(response){

            var $container = $(this.dom),
                value = response && response.base ? parseInt(response.base.integer) : -1;

            $container.find('input[value="' + value + '"]').prop('checked', true);
        },
        /**
         * Get the response in the json format described in
         * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * 
         * @param {Object} interaction
         * @returns {Object}
         */
        getResponse : function(){

            var $container = $(this.dom),
                value = $container.find('input:checked').val() || -1;

            return {base : {integer : value}};
        },
        /**
         * Remove the current response set in the interaction
         * The state may not be restored at this point.
         * 
         * @param {Object} interaction
         */
        resetResponse : function(){

            var $container = $(this.dom);

            $container.find('input').prop('checked', false);
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

            console.log('state set to', state)
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

    _pciContext.register(likertScaleInteraction);
});