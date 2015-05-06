/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicAssociateInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, __, stateFactory, Correct, GraphicAssociateInteraction, helper, PciResponse){

    /**
     * Initialize the state: use the common renderer to set the correct response.
     */
    function initCorrectState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var corrects  = _.values(response.getCorrect());

        GraphicAssociateInteraction.destroy(interaction);

        if(!interaction.paper){
            return;
        }
        
        //add a specific instruction
        helper.appendInstruction(interaction, __('Please set the correct associations by linking the choices.'));
        
        //use the common Renderer
        GraphicAssociateInteraction.render.call(interaction.getRenderer(), interaction);

        GraphicAssociateInteraction.setResponse(
            interaction, 
            PciResponse.serialize(_.invoke(corrects, String.prototype.split, ' '), interaction)
        );

        widget.$container.on('responseChange.qti-widget', function(e, data){
           if(data.response && data.response.list){
                response.setCorrect(
                    _.map(data.response.list.pair, function(pair){
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
        GraphicAssociateInteraction.destroy(interaction); 

        //initialize again the widget's paper
        interaction.paper = widget.createPaper();
        widget.createChoices();
    }

    /**
     * The correct answer state for the graphicAssociate interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Correct
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicAssociateInteraction/states/Correct
     */
    return stateFactory.create(Correct, initCorrectState, exitCorrectState);
});
