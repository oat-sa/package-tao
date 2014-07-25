define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    return stateFactory.create('choice', ['question', 'active'], function(){
        throw new Error('state "choice" prototype init method must be implemented');
    },function(){
        throw new Error('state "choice" prototype exit method must be implemented');
    });
});