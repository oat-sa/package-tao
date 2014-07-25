define([
    'lodash',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/MediaInteraction'
], function(_, stateFactory, SleepState, MediaInteractionCommonRenderer) {

    var initSleepState = function initSleepState() {
        var widget = this.widget;
        var interaction = widget.element;
        widget.$original.data('creator', true); 
        
        if (!widget.$container.find('.mejs-container').length ) {
            widget.mediaElementObject = MediaInteractionCommonRenderer.render.call(interaction.getRenderer(), interaction);    
        }
    };


    return stateFactory.extend(SleepState, initSleepState, _.noop);
});
