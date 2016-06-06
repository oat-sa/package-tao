define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GapMatchInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, __,stateFactory, Correct, commonRenderer, instructionMgr, PciResponse){

    var GapMatchInteractionStateCorrect = stateFactory.create(Correct, function(){

        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();

        var corrects  = _.values(response.getCorrect());
       
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);
        
        //add a specific instruction
        instructionMgr.appendInstruction(interaction, __('Please fill the gap with the correct choices.'));
       
        //use the common Renderer
        commonRenderer.render.call(interaction.getRenderer(), interaction);
   
        commonRenderer.setResponse(
            interaction, 
            PciResponse.serialize(_.invoke(corrects, String.prototype.split, ' '), interaction)
        );

        widget.$container.on('responseChange.qti-widget', function(e, data){
           var type = response.attr('cardinality') === 'single' ? 'base' : 'list';
           if(data.response && data.response[type]){
                if(type === 'base'){
                    response.setCorrect(data.response.base.directedPair.join(' '));
                } else {
                    response.setCorrect(   
                        _.map(data.response.list.directedPair, function(pair){
                            return pair.join(' ');
                        })
                    ); 
                }
           }
        });

    }, function(){
        var widget = this.widget;
        var interaction = this.widget.element;
        
        //stop listening responses changes
        widget.$container.off('responseChange.qti-widget');

        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);

        instructionMgr.removeInstructions(interaction);
    });

    return GapMatchInteractionStateCorrect;
});
