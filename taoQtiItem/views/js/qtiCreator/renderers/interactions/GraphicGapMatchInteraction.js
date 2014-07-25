/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicGapMatchInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/Widget'
], function(_, GraphicGapMatchInteraction, GraphicGapMatchInteractionWidget){
        
    /**
     * The CreatorGraphicGapMatchInteraction  Renderer 
     * @extends taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicGapMatchInteraction
     * @exports taoQtiItem/qtiCreator/renderers/interactions/GraphicGapMatchInteraction
     */
    var CreatorGraphicGapMatchInteraction = _.clone(GraphicGapMatchInteraction);

    /**
     * Render the interaction for authoring.
     * It delegates the rendering to the widget layer.
     * @param {Object} interaction - the model
     * @param {Object} [options] - extra options
     */
    CreatorGraphicGapMatchInteraction.render = function(interaction, options){
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return GraphicGapMatchInteractionWidget.build(
            interaction,
            GraphicGapMatchInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorGraphicGapMatchInteraction;
});
