define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/mediaInteraction/states/states',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/MediaInteraction'
], function($, _, Widget, states, commonRenderer){
    
    var MediaInteractionWidget = _.extend(Widget.clone(), {

        initCreator : function(){
            var self        = this; 
            var $container  = this.$original;
            var $item       = $container.parents('.qti-item');

            this.registerStates(states);            
            Widget.initCreator.call(this);
            
            $item
              .off('.' + this.element.serial)
              .on('resize.'+ this.element.serial, _.throttle(function(e){
                    var width = $container.innerWidth();
                    if(width > 0){
                        self.element.object.attr('width', $container.innerWidth());
                        self.destroyInteraction();
                        self.renderInteraction();
                    }
            }, 100));
        },
    
        destroy : function(){

            var $container = this.$original;
            var $item      = $container.parents('.qti-item');

            //stop listening the resize
            $item.off('resize.' + this.element.serial);

            //call parent destroy
            Widget.destroy.call(this);
        },

        renderInteraction : function(){
            var $container  = this.$original; 
            var interaction = this.element;
            commonRenderer.render.call(interaction.getRenderer(), interaction, {
                features : 'full',
                controlPlaying : false
            });
        },

        destroyInteraction : function(){
            var interaction = this.element;
            commonRenderer.resetResponse.call(interaction.getRenderer(), interaction);    
            commonRenderer.destroy.call(interaction.getRenderer(), interaction);    
        }
    });

    return MediaInteractionWidget;
});
