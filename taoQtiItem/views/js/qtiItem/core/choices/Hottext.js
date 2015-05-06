define(['taoQtiItem/qtiItem/core/choices/Choice', 'taoQtiItem/qtiItem/mixin/ContainerInline'], function(Choice, Container){
    
    var Hottext = Choice.extend({
        qtiClass : 'hottext'
    });
    
    Container.augment(Hottext);
    
    return Hottext;
});


