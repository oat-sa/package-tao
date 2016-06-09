define(['taoQtiItem/qtiItem/core/interactions/BlockInteraction'], function(BlockInteraction){
    var OrderInteraction = BlockInteraction.extend({
        qtiClass : 'orderInteraction'
    });
    return OrderInteraction;
});