define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/endAttemptInteraction/states/Question'
], function(factory, states){
    'use strict';
    return factory.createBundle(states, arguments, ['answer', 'correct', 'map']);
});