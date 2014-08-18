define(['taoQtiItem/qtiCreator/widgets/interactions/Widget'], function(Widget){

    var InlineInteractionWidget = Widget.clone();

    InlineInteractionWidget.destroy = function(){
        
        //remove all events on the original DOM element
        this.$original.off('.qti-widget');
        
        //remove the external container completely
        this.$container.remove();

        //clean old referenced event
        this.offEvents();
    };

    return InlineInteractionWidget;
});