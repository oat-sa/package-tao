define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/textEntryInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/textEntryInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/textEntryInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/textEntryInteraction/states/Map'
], function(factory, states){
    return factory.createBundle(states, arguments);
});