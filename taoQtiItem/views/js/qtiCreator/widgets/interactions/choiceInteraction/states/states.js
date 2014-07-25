define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/states/Map'
], function(factory, states){
    return factory.createBundle(states, arguments);
});