/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicGapMatchInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, __, stateFactory, Correct, GraphicGapMatchInteraction, helper, PciResponse){

    /**
     * Initialize the state: use the common renderer to set the correct response.
     */
    function initCorrectState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var corrects  = _.values(response.getCorrect());
        
        GraphicGapMatchInteraction.destroy(interaction);

        if(!interaction.paper){
            return;
        }
        
        //add a specific instruction
        helper.appendInstruction(interaction, __('Please fill the gap with the correct choices below.'));
       
        widget.createGapImgs(); 
 
        //use the common Renderer
        GraphicGapMatchInteraction.render.call(interaction.getRenderer(), interaction);


        GraphicGapMatchInteraction.setResponse(
            interaction, 
            PciResponse.serialize(_.invoke(corrects, String.prototype.split, ' '), interaction)
        );

        widget.$container.on('responseChange.qti-widget', function(e, data){
           if(data.response && data.response.list){
                response.setCorrect(
                    _.map(data.response.list.directedPair, function(pair){
                        return pair.join(' ');
                    })
                ); 
           }
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
        GraphicGapMatchInteraction.destroy(interaction); 

        //initialize again the widget's paper
        interaction.paper = widget.createPaper(_.bind(widget.scaleGapList, widget));
        widget.createChoices();
        widget.createGapImgs();
    }

    /**
     * The correct answer state for the graphicGapMatch interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Correct
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Correct
     */
    return stateFactory.create(Correct, initCorrectState, exitCorrectState);
});
