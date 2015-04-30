define([
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    '{tool-id}/creator/widget/states/states'
], function(Widget, states){

    var {tool-obj}Widget = Widget.clone();

    {tool-obj}Widget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    {tool-obj}Widget.buildContainer = function(){
        
        this.$container = this.$original;
        this.$container.addClass('widget-box');
    };
    
    return {tool-obj}Widget;
});