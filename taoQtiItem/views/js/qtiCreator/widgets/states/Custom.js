define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    return stateFactory.create('custom', ['answer', 'active'], function(){
        throw new Error('state "custom" prototype init method must be implemented');
    },function(){
        throw new Error('state "custom" prototype exit method must be implemented');
    });
});