define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    return stateFactory.create('question', ['active'], function(){
        throw new Error('state "question" prototype init method must be implemented');
    },function(){
        throw new Error('state "question" prototype exit method must be implemented');
    });
});