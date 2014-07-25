define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    return stateFactory.create('correct', ['answer', 'active'], function(){
        throw new Error('state "correct" prototype init method must be implemented');
    },function(){
        throw new Error('state "correct" prototype exit method must be implemented');
    });
});