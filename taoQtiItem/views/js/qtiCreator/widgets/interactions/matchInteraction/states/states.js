define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/matchInteraction/states/Map'
], function(factory, states){
    return factory.createBundle(states, arguments);
});