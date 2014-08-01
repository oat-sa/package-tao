define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/CustomInteraction.amd',
    'taoQtiItem/qtiCreator/helper/pciCreator'
], function(_, CustomInteraction, pciCreator){
    
    var CreatorCustomInteraction = _.clone(CustomInteraction);

    CreatorCustomInteraction.render = function(interaction, options){
        
        var Widget = pciCreator.getPciInstance(interaction).getWidget();
        
        return Widget.build(
            interaction,
            CustomInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorCustomInteraction;
});