define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GapMatchInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/gapMatchInteraction/Widget'
], function(_, GapMatchInteraction, GapMatchInteractionWidget){
    
    var CreatorGapMatchInteraction = _.clone(GapMatchInteraction);

    CreatorGapMatchInteraction.render = function(interaction, options){
        
        return GapMatchInteractionWidget.build(
            interaction,
            GapMatchInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorGapMatchInteraction;
});