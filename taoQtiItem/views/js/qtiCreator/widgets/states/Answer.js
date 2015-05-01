define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    return stateFactory.create('answer', ['active'], function(){
        throw new Error('state "answer" prototype init method must be implemented');
    },function(){
        throw new Error('state "answer" prototype exit method must be implemented');
    });
});