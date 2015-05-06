/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Sleep',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Map'
], function(factory, states){

    //creates a state bundle for the interaction
    return factory.createBundle(states, arguments);
});
