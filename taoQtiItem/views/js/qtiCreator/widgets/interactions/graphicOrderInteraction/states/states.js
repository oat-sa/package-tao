/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/states',
	'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/Sleep',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/Correct'
], function(factory, states){

    //creates a state bundle for the interaction
    return factory.createBundle(states, arguments);
});
