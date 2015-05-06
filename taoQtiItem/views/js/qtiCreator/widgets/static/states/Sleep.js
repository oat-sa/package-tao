define(['taoQtiItem/qtiCreator/widgets/states/factory', 'taoQtiItem/qtiCreator/widgets/states/Sleep'], function(stateFactory, SleepState){
    
    var StaticStateSleep = stateFactory.extend(SleepState, function(){
        
        var _widget = this.widget;
        
        _widget.$container.on('click.qti-widget.sleep', function(e){
            e.stopPropagation();
            _widget.changeState('active');
        });
        
    }, function(){
        
        var _widget = this.widget;
        
        //remove hover outline box display
        _widget.$container.off('.sleep');
    });
    
    return StaticStateSleep;
});