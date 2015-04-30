/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/SelectPointInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/selectPointInteraction/Widget'
], function(_, SelectPointInteraction, SelectPointInteractionWidget){
        
    /**
     * The CreatorSelectPointInteraction  Renderer 
     * @extends taoQtiItem/qtiCommonRenderer/renderers/interactions/SelectPointInteraction
     * @exports taoQtiItem/qtiCreator/renderers/interactions/SelectPointInteraction
     */
    var CreatorSelectPointInteraction = _.clone(SelectPointInteraction);

    /**
     * Render the interaction for authoring.
     * It delegates the rendering to the widget layer.
     * @param {Object} interaction - the model
     * @param {Object} [options] - extra options
     */
    CreatorSelectPointInteraction.render = function(interaction, options){
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return SelectPointInteractionWidget.build(
            interaction,
            SelectPointInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorSelectPointInteraction;
});
