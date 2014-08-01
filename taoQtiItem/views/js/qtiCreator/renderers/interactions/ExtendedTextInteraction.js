define([
'lodash',
'taoQtiItem/qtiCommonRenderer/renderers/interactions/ExtendedTextInteraction',
'taoQtiItem/qtiCreator/widgets/interactions/extendedTextInteraction/Widget'
], function(_, ExtendedTextInteraction,ExtendedTextInteractionWidget){
    
    var CreatorExtendedTextInteraction = _.clone(ExtendedTextInteraction);
    
    CreatorExtendedTextInteraction.render = function(interaction, options) {
        
        ExtendedTextInteraction.render(interaction);
        
        return ExtendedTextInteractionWidget.build(
                interaction,
                ExtendedTextInteraction.getContainer(interaction),
                this.getOption('interactionOptionForm'),
                this.getOption('responseOptionForm'),
                options
        );
    };
    
    return CreatorExtendedTextInteraction;
});