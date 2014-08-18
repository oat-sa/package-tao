define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/TextEntryInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/textEntryInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/textEntryInteraction.placeholder'
], function(_, TextEntryInteraction, TextEntryInteractionWidget, tpl){

    var CreatorTextEntryInteraction = _.clone(TextEntryInteraction);

    CreatorTextEntryInteraction.template = tpl;

    CreatorTextEntryInteraction.render = function(interaction, options){

        //need to pass choice option form to the interaction widget because it will manage everything
        options = options || {};

        return TextEntryInteractionWidget.build(
            interaction,
            $('.textEntryInteraction-placeholder[data-serial="' + interaction.serial + '"]'),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorTextEntryInteraction;
});