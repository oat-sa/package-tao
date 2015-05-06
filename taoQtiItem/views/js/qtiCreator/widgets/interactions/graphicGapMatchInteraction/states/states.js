/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Sleep',    
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Map'
], function(factory, states){

    //creates a state bundle for the interaction
    return factory.createBundle(states, arguments);
});
