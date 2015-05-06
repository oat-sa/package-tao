define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/Choice',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/Map'
], function(factory, states){
    return factory.createBundle(states, arguments);
});