define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/uploadInteraction/states/states'
], function(Widget, states){

    var UploadInteractionWidget = Widget.clone();

    UploadInteractionWidget.initCreator = function(){
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
    };

    return UploadInteractionWidget;
});