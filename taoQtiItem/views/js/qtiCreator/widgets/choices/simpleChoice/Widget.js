define([
    'taoQtiItem/qtiCreator/widgets/choices/Widget',
    'taoQtiItem/qtiCreator/widgets/choices/simpleChoice/states/states'
], function(Widget, states){

    var SimpleChoiceWidget = Widget.clone();

    SimpleChoiceWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.registerStates(states);

        //choiceInteraction:
        //prevent checkbox/radio from being selectable
        var $realLabel = this.$container.find('.real-label');
        $realLabel.children('input').attr('disabled', 'disabled');
    };

    return SimpleChoiceWidget;
});