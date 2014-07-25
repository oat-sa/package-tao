/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/HotspotInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/Widget'
], function(_, HotspotInteraction, HotspotInteractionWidget){
        
    /**
     * The CreatorHotspotInteraction  Renderer 
     * @extends taoQtiItem/qtiCommonRenderer/renderers/interactions/HotspotInteraction
     * @exports taoQtiItem/qtiCreator/renderers/interactions/HotspotInteraction
     */
    var CreatorHotspotInteraction = _.clone(HotspotInteraction);

    /**
     * Render the interaction for authoring.
     * It delegates the rendering to the widget layer.
     * @param {Object} interaction - the model
     * @param {Object} [options] - extra options
     */
    CreatorHotspotInteraction.render = function(interaction, options){
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return HotspotInteractionWidget.build(
            interaction,
            HotspotInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorHotspotInteraction;
});
