define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/mediaInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/mediaInteraction/states/Sleep'
], function(factory, states){
    return factory.createBundle(states, arguments, ['answer', 'correct', 'map']);
});
