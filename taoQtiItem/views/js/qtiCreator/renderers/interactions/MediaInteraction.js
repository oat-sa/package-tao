define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/MediaInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/mediaInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/interactions/mediaInteraction'
], function(_, MediaInteraction, MediaInteractionWidget, tpl){
    
    var MediaInteraction = _.clone(MediaInteraction);

    MediaInteraction.template = tpl;
    
    MediaInteraction.render = function(interaction, options){
        
        
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        //options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return MediaInteractionWidget.build(
            interaction,
            MediaInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),//note : no response required...
            options
        );
    };

    return MediaInteraction;
});