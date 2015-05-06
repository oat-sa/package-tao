define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Sleep'
], function(stateFactory, SleepState){
    
    var ObjectStateSleep = stateFactory.create(SleepState, function(){
        
    }, function(){
        
    });
    
    return ObjectStateSleep;
});