define([
    'taoQtiItem/qtiCreator/widgets/choices/Widget',
    'taoQtiItem/qtiCreator/widgets/choices/inlineChoice/states/states'
], function(Widget, states){

    var InlineChoiceWidget = Widget.clone();

    InlineChoiceWidget.initCreator = function(){
        
        Widget.initCreator.call(this);

        this.registerStates(states);
    };

    return InlineChoiceWidget;
});