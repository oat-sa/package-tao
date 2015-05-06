define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/containerInteraction/states/Question'
], function(factory, states){
    return factory.createBundle(states, arguments);
});