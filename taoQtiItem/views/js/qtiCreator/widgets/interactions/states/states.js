define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Active',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Choice',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Map',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Custom'
], function(factory, states){
    return factory.createBundle(states, arguments);
});