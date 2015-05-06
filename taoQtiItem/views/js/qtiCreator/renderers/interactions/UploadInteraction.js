define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/UploadInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/uploadInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/interactions/uploadInteraction'
], function(_, UploadInteraction, UploadInteractionWidget, tpl){
    
    var UploadInteraction = _.clone(UploadInteraction);

    UploadInteraction.template = tpl;
    UploadInteraction.render = function(interaction, options){
        
        UploadInteraction.resetGui(interaction);
        
        return UploadInteractionWidget.build(
            interaction,
            UploadInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),//note : no response required...
            options
        );
    };

    return UploadInteraction;
});