define(['taoQtiItem/qtiItem/core/interactions/ContainerInteraction', 'taoQtiItem/qtiItem/core/Element'], function(ContainerInteraction, Element){
    var HottextInteraction = ContainerInteraction.extend({
        qtiClass : 'hottextInteraction',
        getChoices : function(){
            return this.getBody().getElements('hottext');
        },
        getChoice : function(serial){
            var element = this.getBody().getElement(serial);
            return Element.isA(element, 'choice') ? element : null;
        }
    });
    return HottextInteraction;
});


