define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/OrderInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/orderInteraction/Widget'
], function(_, OrderInteraction, OrderInteractionWidget){

    var CreatorOrderInteraction = _.clone(OrderInteraction);

    CreatorOrderInteraction.render = function(interaction, options){
        
        return OrderInteractionWidget.build(
            interaction,
            OrderInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorOrderInteraction;
});