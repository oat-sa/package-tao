define(['taoQtiItem/qtiItem/core/interactions/ContainerInteraction'], function(ContainerInteraction){
    var GapMatchInteraction = ContainerInteraction.extend({
        qtiClass : 'gapMatchInteraction',
        getGaps : function(){
            return this.getBody().getElements('gap');
        }
    });
    return GapMatchInteraction;
});
