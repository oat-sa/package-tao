define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/uploadInteraction/states/Question'
], function(factory, states){
    //remove answer state, which does not make much sense here
    return factory.createBundle(states, arguments, ['answer', 'correct', 'map']);
});