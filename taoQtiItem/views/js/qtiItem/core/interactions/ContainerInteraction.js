define(['taoQtiItem/qtiItem/core/interactions/BlockInteraction', 'taoQtiItem/qtiItem/mixin/Container'], function(BlockInteraction, Container){
    var ContainerInteraction = BlockInteraction.extend({});
    Container.augment(ContainerInteraction);
    return ContainerInteraction;
});