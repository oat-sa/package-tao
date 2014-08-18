define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/states',
    'taoQtiItem/qtiCreator/widgets/choices/states/Sleep',
    'taoQtiItem/qtiCreator/widgets/choices/states/Active',
    'taoQtiItem/qtiCreator/widgets/choices/states/Question',
    'taoQtiItem/qtiCreator/widgets/choices/states/Answer'
], function(factory, states){
    return factory.createBundle(states, arguments);
});