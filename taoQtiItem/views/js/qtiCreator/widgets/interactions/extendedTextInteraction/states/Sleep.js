define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/ExtendedTextInteraction'
], function (stateFactory, Sleep, renderer) {
    'use strict';
    var ExtendedTextInteractionStateSleep = stateFactory.extend(Sleep, function () {
        renderer.disable(this.widget.element);
    }, function () {
        
    });
    return ExtendedTextInteractionStateSleep;
});
