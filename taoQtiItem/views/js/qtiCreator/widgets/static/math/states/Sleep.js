define(['taoQtiItem/qtiCreator/widgets/states/factory', 'taoQtiItem/qtiCreator/widgets/static/states/Sleep'], function(stateFactory, SleepState){
    
    var MathStateSleep = stateFactory.extend(SleepState, function(){}, function(){});
    
    return MathStateSleep;
});