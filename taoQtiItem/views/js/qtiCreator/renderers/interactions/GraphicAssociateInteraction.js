/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicAssociateInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicAssociateInteraction/Widget'
], function(_, GraphicAssociateInteraction, GraphicAssociateInteractionWidget){
        
    /**
     * The CreatorGraphicAssociateInteraction  Renderer 
     * @extends taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicAssociateInteraction
     * @exports taoQtiItem/qtiCreator/renderers/interactions/GraphicAssociateInteraction
     */
    var CreatorGraphicAssociateInteraction = _.clone(GraphicAssociateInteraction);

    /**
     * Render the interaction for authoring.
     * It delegates the rendering to the widget layer.
     * @param {Object} interaction - the model
     * @param {Object} [options] - extra options
     */
    CreatorGraphicAssociateInteraction.render = function(interaction, options){
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return GraphicAssociateInteractionWidget.build(
            interaction,
            GraphicAssociateInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorGraphicAssociateInteraction;
});
