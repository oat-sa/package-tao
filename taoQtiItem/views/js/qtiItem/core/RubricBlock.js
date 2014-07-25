define(['taoQtiItem/qtiItem/core/Element', 'taoQtiItem/qtiItem/mixin/Container'], function(Element, Container){

    var RubricBlock = Element.extend({
        qtiClass : 'rubricBlock'
    });
    
    Container.augment(RubricBlock);
    
    return RubricBlock;
});