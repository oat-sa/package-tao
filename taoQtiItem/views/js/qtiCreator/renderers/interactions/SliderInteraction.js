define([
'lodash',
'taoQtiItem/qtiCommonRenderer/renderers/interactions/SliderInteraction',
'taoQtiItem/qtiCreator/widgets/interactions/sliderInteraction/Widget'
], function(_, SliderInteraction, SliderInteractionWidget){
    
    var CreatorSliderInteraction = _.clone(SliderInteraction);
    
    CreatorSliderInteraction.render = function(interaction, options){
        
        SliderInteraction.render(interaction);
        
        return SliderInteractionWidget.build(
                interaction,
                SliderInteraction.getContainer(interaction),
                this.getOption('interactionOptionForm'),
                this.getOption('responseOptionForm'),
                options
        );
    };
    
    return CreatorSliderInteraction;
});