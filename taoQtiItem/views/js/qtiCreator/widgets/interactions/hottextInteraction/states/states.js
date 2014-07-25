define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/containerInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/hottextInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/hottextInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/hottextInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/hottextInteraction/states/Map'
], function(factory, states){
    return factory.createBundle(states, arguments);
});