define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    return stateFactory.create('map', ['answer', 'active'], function(){
        throw new Error('state "map" prototype init method must be implemented');
    },function(){
        throw new Error('state "map" prototype exit method must be implemented');
    });
});