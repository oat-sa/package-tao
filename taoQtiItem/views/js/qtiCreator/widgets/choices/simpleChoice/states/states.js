define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/states',
    'taoQtiItem/qtiCreator/widgets/choices/simpleChoice/states/Choice'
], function(factory, states){
    return factory.createBundle(states, arguments);
});