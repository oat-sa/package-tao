define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/MatchInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/Widget'
], function(_, MatchInteraction,MatchInteractionWidget){

    var CreatorMatchInteraction = _.clone(MatchInteraction);

    CreatorMatchInteraction.render = function(interaction, options){
        
        return MatchInteractionWidget.build(
            interaction,
            MatchInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorMatchInteraction;
});