define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/states',
    'taoQtiItem/qtiCreator/widgets/static/portableInfoControl/states/Sleep'
], function(factory, states){
    return factory.createBundle(states, arguments, ['answer', 'correct', 'map']);
});