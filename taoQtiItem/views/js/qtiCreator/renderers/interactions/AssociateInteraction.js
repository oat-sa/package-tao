define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/AssociateInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/Widget'
], function(_, AssociateInteraction, AssociateInteractionWidget){

    var CreatorAssociateInteraction = _.clone(AssociateInteraction);

    CreatorAssociateInteraction.render = function(interaction, options){
        
        AssociateInteraction.renderEmptyPairs(interaction);
        
        return AssociateInteractionWidget.build(
            interaction,
            AssociateInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorAssociateInteraction;
});