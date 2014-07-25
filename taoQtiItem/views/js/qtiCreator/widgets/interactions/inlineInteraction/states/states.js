define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineInteraction/states/Sleep',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineInteraction/states/Active'
], function(factory, states){
    return factory.createBundle(states, arguments);
});