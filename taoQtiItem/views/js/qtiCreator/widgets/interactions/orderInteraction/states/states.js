define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/orderInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/orderInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/orderInteraction/states/Correct'
], function(factory, states){
    return factory.createBundle(states, arguments);
});