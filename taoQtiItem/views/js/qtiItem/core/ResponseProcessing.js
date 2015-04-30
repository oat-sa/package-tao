define(['taoQtiItem/qtiItem/core/Element', 'lodash'], function(Element, _){
    
    var ResponseProcessing = Element.extend({
        qtiClass : 'responseProcessing',
        processingType : '',
        xml : ''
    });
    
    return ResponseProcessing;
});