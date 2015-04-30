/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicOrderInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, __, stateFactory, Correct, commonRenderer, instructionMgr, PciResponse){

    /**
     * Initialize the state: use the common renderer to set the correct response.
     */
    function initCorrectState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
       
        //really need to destroy before ? 
        commonRenderer.resetResponse(interaction);
        commonRenderer.destroy(interaction);

        if(!interaction.paper){
            return;
        }
        
        //add a specific instruction
        instructionMgr.appendInstruction(interaction, __('Please order the choices below to set the correct answer'));
        
        //use the common Renderer
        commonRenderer.render.call(interaction.getRenderer(), interaction);

        commonRenderer.setResponse(interaction, PciResponse.serialize(_.values(response.getCorrect()), interaction));

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
        commonRenderer.resetResponse(interaction);
        commonRenderer.destroy(interaction); 
        instructionMgr.removeInstructions(interaction);

        //initialize again the widget's paper
        interaction.paper = widget.createPaper(_.bind(widget.scaleOrderList, widget)); 
        widget.createChoices();
        widget.renderOrderList();
    }

    /**
     * The correct answer state for the graphicOrder interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Correct
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/Correct
     */
    return stateFactory.create(Correct, initCorrectState, exitCorrectState);
});
