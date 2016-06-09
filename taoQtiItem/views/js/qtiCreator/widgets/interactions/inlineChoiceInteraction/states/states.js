define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineChoiceInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineChoiceInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineChoiceInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineChoiceInteraction/states/Map'
], function(factory, states){
    return factory.createBundle(states, arguments);
});