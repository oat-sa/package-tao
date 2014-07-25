define(['taoQtiItem/qtiCreator/widgets/states/factory', 'taoQtiItem/qtiCreator/widgets/states/Sleep'], function(stateFactory, SleepState){
    
    var InteractionStateSleep = stateFactory.extend(SleepState, function(){
        
        var _widget = this.widget;
        
        _widget.$container.on('click.qti-widget.sleep', function(e){
            e.stopPropagation();
            //if active == false do this: (else nothing)
            //show toolbar, prompt widget and choice widgets and property form

            //init default mode: question
            _widget.changeState('question');
        });
        
    }, function(){
        
        var _widget = this.widget;
        
        //remove hover outline box display
        _widget.$container.off('.sleep');
    });
    
    return InteractionStateSleep;
});