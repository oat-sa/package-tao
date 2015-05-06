/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/HotspotInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, __, stateFactory, Correct, HotspotInteraction, helper, PciResponse){

    /**
     * Initialize the state: use the common renderer to set the correct response.
     */
    function initCorrectState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        

        //really need to destroy before ? 
        HotspotInteraction.destroy(interaction);

        if(!interaction.paper){
            return;
        }
        
        //add a specific instruction
        helper.appendInstruction(interaction, __('Please select the correct hotspot choices below.'));
        
        //use the common Renderer
        HotspotInteraction.render.call(interaction.getRenderer(), interaction);

        HotspotInteraction.setResponse(interaction, PciResponse.serialize(_.values(response.getCorrect()), interaction));

        widget.$container.on('responseChange.qti-widget', function(e, data){
            response.setCorrect(PciResponse.unserialize(data.response, interaction));
        });
    }

    /**
     * Exit the correct state
     */
    function exitCorrectState(){
        var widget = this.widget;
        var interaction = widget.element;
        
        if(!interaction.paper){
            return;
        }

        //stop listening responses changes
        widget.$container.off('responseChange.qti-widget');
        
        //destroy the common renderer
        helper.removeInstructions(interaction);
        HotspotInteraction.destroy(interaction); 

        //initialize again the widget's paper
        interaction.paper = widget.createPaper();
        widget.createChoices(); 
    }

    /**
     * The correct answer state for the hotspot interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Correct
     * @exports taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Correct
     */
    return stateFactory.create(Correct, initCorrectState, exitCorrectState);
});
