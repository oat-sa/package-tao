define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/states',
    'taoQtiItem/qtiCreator/widgets/choices/gap/states/Choice',
    'taoQtiItem/qtiCreator/widgets/choices/gap/states/Question'
], function(factory, states){
    return factory.createBundle(states, arguments);
});