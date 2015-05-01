define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/ChoiceInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/Widget'
], function(_, ChoiceInteraction, ChoiceInteractionWidget){
    
    var CreatorChoiceInteraction = _.clone(ChoiceInteraction);

    CreatorChoiceInteraction.render = function(interaction, options){
        
        return ChoiceInteractionWidget.build(
            interaction,
            ChoiceInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorChoiceInteraction;
});