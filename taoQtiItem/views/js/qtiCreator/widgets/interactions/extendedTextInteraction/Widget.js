define([
'taoQtiItem/qtiCreator/widgets/interactions/Widget',
'taoQtiItem/qtiCreator/widgets/interactions/extendedTextInteraction/states/states'
], function(Widget, states){
    'use strict';
    var ExtendedTextInteractionWidget = Widget.clone();

    ExtendedTextInteractionWidget.initCreator = function(){
        this.registerStates(states);
        Widget.initCreator.call(this);
    };

    return ExtendedTextInteractionWidget;
});
