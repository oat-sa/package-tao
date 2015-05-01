define([
    'taoQtiItem/qtiCreator/widgets/states/factory', 
    'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep'
], function(stateFactory, SleepState){
   
    var initSleepState = function initSleepState(){
        var widget      = this.widget;
        var interaction = widget.element;
        widget.on('metaChange', function(data){
            if(data.key === 'responsive'){
                if(data.value === true){
                    interaction.addClass('responsive');
                } else {
                    interaction.removeClass('responsive');
                }
                widget.rebuild();
            }
        });
    };

    var exitSleepState = function exitSleepState(){
        var widget      = this.widget;
        $('.image-editor.solid, .block-listing.source', widget.$container).css('min-width', 0);
    };
 
    return stateFactory.extend(SleepState, initSleepState, exitSleepState); 
});
