define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/states',
    'taoQtiItem/qtiCreator/widgets/choices/inlineChoice/states/Question',
    'taoQtiItem/qtiCreator/widgets/choices/inlineChoice/states/Choice'
], function(factory, states){
    return factory.createBundle(states, arguments);
});