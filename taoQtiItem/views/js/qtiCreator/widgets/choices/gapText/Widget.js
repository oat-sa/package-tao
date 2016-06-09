define([
    'taoQtiItem/qtiCreator/widgets/choices/Widget',
    'taoQtiItem/qtiCreator/widgets/choices/gapText/states/states'
], function(Widget, states){

    var GapTextWidget = Widget.clone();

    GapTextWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.registerStates(states);
        
    };

    return GapTextWidget;
});