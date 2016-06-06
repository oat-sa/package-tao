define([
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/mixin/ContainerInline',
    'taoQtiItem/qtiItem/mixin/NamespacedElement'
], function(Element, Container, NamespacedElement){
    
    var Include = Element.extend({
        qtiClass : 'include',
        defaultNsName : 'xi',
        defaultNsUri : 'http://www.w3.org/2001/XInclude',
        nsUriFragment : 'XInclude',
        isEmpty : function(){
            return (!this.attr('href') || this.getBody().isEmpty());
        }
    });
    Container.augment(Include);
    NamespacedElement.augment(Include);
    return Include;
});
