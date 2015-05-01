define([
'taoQtiItem/qtiCreator/widgets/states/factory',
'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
'taoQtiItem/qtiCreator/widgets/interactions/extendedTextInteraction/states/Question',
'taoQtiItem/qtiCreator/widgets/interactions/extendedTextInteraction/states/Answer',
'taoQtiItem/qtiCreator/widgets/interactions/extendedTextInteraction/states/Correct',

], function(factory, states){
    'use strict';
    return factory.createBundle(states, arguments);
});
