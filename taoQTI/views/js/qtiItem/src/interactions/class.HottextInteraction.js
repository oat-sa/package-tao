Qti.HottextInteraction = Qti.ContainerInteraction.extend({
    qtiTag: 'hottextInteraction',
    getChoices : function(){
        return this.getBody().getElements('Hottext');
    }
});


