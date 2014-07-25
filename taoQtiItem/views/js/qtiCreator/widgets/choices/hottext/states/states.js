define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/states',
    'taoQtiItem/qtiCreator/widgets/choices/hottext/states/Choice',
    'taoQtiItem/qtiCreator/widgets/choices/hottext/states/Question'
], function(factory, states){
    return factory.createBundle(states, arguments);
});