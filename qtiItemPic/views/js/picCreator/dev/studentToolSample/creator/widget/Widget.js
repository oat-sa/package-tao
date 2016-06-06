define([
    'taoQtiItem/qtiCreator/widgets/Widget',
    'studentToolSample/creator/widget/states/states'
], function(Widget, states){

    var StudentToolSampleWidget = Widget.clone();

    StudentToolSampleWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };
    
    StudentToolSampleWidget.buildContainer = function(){
        
        this.$container = this.$original;
        this.$container.addClass('widget-box');
    };
    
    return StudentToolSampleWidget;
});