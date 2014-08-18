define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/HottextInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/hottextInteraction/Widget'
], function(_, HottextInteraction, HottextInteractionWidget){
    
    var CreatorHottextInteraction = _.clone(HottextInteraction);

    CreatorHottextInteraction.render = function(interaction, options){
        
        return HottextInteractionWidget.build(
            interaction,
            HottextInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorHottextInteraction;
});