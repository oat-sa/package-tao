define([
'taoQtiItem/qtiCreator/widgets/interactions/Widget',
'taoQtiItem/qtiCreator/widgets/interactions/sliderInteraction/states/states'
], function(Widget, states){
    
    var SliderInteractionWidget = Widget.clone();
    
    SliderInteractionWidget.initCreator = function(){
        this.registerStates(states);
        Widget.initCreator.call(this);
        
        // Disable slider until response edition.
        this.$container.find('.qti-slider').attr('disabled', 'disabled');
    };
    
    return SliderInteractionWidget;
});