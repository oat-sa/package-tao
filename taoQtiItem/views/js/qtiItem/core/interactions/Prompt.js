define(['taoQtiItem/qtiItem/core/Element', 'taoQtiItem/qtiItem/mixin/ContainerInline'], function(Element, Container){
    var Prompt = Element.extend({qtiClass : 'prompt'});
    Container.augment(Prompt);
    return Prompt;
});