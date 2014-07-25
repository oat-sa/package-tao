define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/InlineChoiceInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineChoiceInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/inlineChoiceInteraction.placeholder'
], function(_, InlineChoiceInteraction, InlineChoiceInteractionWidget, tpl){

    var CreatorInlineChoiceInteraction = _.clone(InlineChoiceInteraction);

    CreatorInlineChoiceInteraction.template = tpl;

    CreatorInlineChoiceInteraction.render = function(interaction, options){

        //need to pass choice option form to the interaction widget because it will manage everything
        options = options || {};
        options.choiceOptionForm = this.getOption('choiceOptionForm');

        return InlineChoiceInteractionWidget.build(
            interaction,
            $('.inlineChoiceInteraction-placeholder[data-serial="' + interaction.serial + '"]'),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorInlineChoiceInteraction;
});