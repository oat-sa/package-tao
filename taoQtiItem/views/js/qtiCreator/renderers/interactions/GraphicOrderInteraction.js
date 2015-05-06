/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicOrderInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/Widget'
], function(_, GraphicOrderInteraction, GraphicOrderInteractionWidget){
        
    /**
     * The CreatorGraphicOrderInteraction  Renderer 
     * @extends taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicOrderInteraction
     * @exports taoQtiItem/qtiCreator/renderers/interactions/GraphicOrderInteraction
     */
    var CreatorGraphicOrderInteraction = _.clone(GraphicOrderInteraction);

    /**
     * Render the interaction for authoring.
     * It delegates the rendering to the widget layer.
     * @param {Object} interaction - the model
     * @param {Object} [options] - extra options
     */
    CreatorGraphicOrderInteraction.render = function(interaction, options){
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return GraphicOrderInteractionWidget.build(
            interaction,
            GraphicOrderInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorGraphicOrderInteraction;
});
