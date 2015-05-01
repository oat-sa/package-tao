define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/EndAttemptInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/endAttemptInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/endAttemptInteraction.placeholder'
], function($, _, EndAttemptInteraction, EndAttemptInteractionWidget, tpl){
    'use strict';
    var CreatorEndAttemptInteraction = _.clone(EndAttemptInteraction);

    CreatorEndAttemptInteraction.template = tpl;
    
    /**
     * Render the end attempt interaction widget
     * 
     * @param {Object} interaction - the qti js object
     * @param {Object} options
     * @returns {Object}
     */
    CreatorEndAttemptInteraction.render = function(interaction, options){
        
        //need to pass choice option form to the interaction widget because it will manage everything
        options = options || {};
        
        //complete the option
        var interactionsConfig = this.getOption('interactions');
        if(interactionsConfig.endAttempt){
            options.config = interactionsConfig.endAttempt;
        }
        
        return EndAttemptInteractionWidget.build(
            interaction,
            $('.endAttemptInteraction-placeholder[data-serial="' + interaction.serial + '"]'),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorEndAttemptInteraction;
});
