define([
    'taoQtiItem/qtiCreator/widgets/choices/Widget',
    'taoQtiItem/qtiCreator/widgets/choices/hottext/states/states'
], function(Widget, states){

    var HottextWidget = Widget.clone();

    HottextWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.registerStates(states);
        
    };
    
    HottextWidget.buildContainer = function(){
        this.$container = this.$original.addClass('widget-box');
        this.$container.attr('contenteditable', false);
        this.$original.find('.hottext-checkmark > input').prop('disabled', 'disabled');
    };
    
    return HottextWidget;
});