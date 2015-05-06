define(['jquery'], function($){
    
    /**
     * Global object qtiCustomInteractionContext
     * Cannot be passed as AMD dependency because the pci libs are loaded under a different requirejs context.
     * This means that the dependencies of qtiCustomInteractionContext would also need to be compied into this one which is too complicated.
     * 
     * @type @exp;window@pro;qtiCustomInteractionContext
     */
    var _pciContext = window.qtiCustomInteractionContext;
    
    var likertScaleInteraction = {
        id : -1,
        getTypeIdentifier : function(){
            return 'likertScaleInteraction';
        },
        initialize : function(id, dom, config, state, response){

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
            var $labelMin = $('<label>', {'class':'likert-label likert-label-min'}).html(config['label-min']);
            var $labelMax = $('<label>', {'class':'likert-label likert-label-max'}).html(config['label-max']);
            
            $ul.find('li:first').prepend($labelMin);
            $ul.find('li:last').append($labelMax);
            
            //add prompt
            $container.find('div.prompt').append(config.prompt);
            
            if(response){
                this.setResponse(response);
            }
            
            _pciContext.notifyReady(this);
            
            var self = this;
            $container.change(function(){
                var r = self.getResponse();
                console.log(r.base.integer);
            });
        },
        setResponse : function(response){
        
            var $container = $(this.dom),
                value = response && response.base ? parseInt(response.base.integer) : -1;
            
            $container.find('input[value="'+value+'"]').prop('checked', true);
        },
        getResponse : function(){
            
            var $container = $(this.dom),
                value = $container.find('input:checked').val() || -1;
            
            return {base : {integer:value}};
        },
        resetResponse : function(){
        
            var $container = $(this.dom);
            
            $container.find('input').prop('checked', false);
        },
        destroy : function(){

            $(this.dom).empty();
        },
        getSerializedState : function(){

            return {};
        }
    };

    _pciContext.register(likertScaleInteraction);
});