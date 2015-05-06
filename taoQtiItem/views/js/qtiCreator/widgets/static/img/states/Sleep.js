define(['taoQtiItem/qtiCreator/widgets/states/factory', 'taoQtiItem/qtiCreator/widgets/static/states/Sleep'], function(stateFactory, SleepState){
    
    var ImgStateSleep = stateFactory.extend(SleepState, function(){}, function(){});
    
    return ImgStateSleep;
});