define(['taoQtiItem/qtiItem/core/Element', 'lodash'], function(Element, _){
    
    var ResponseProcessing = Element.extend({
        qtiClass : 'responseProcessing',
        processingType : '',
        xml : '',
        toArray : function(){
            var arr = this._super();
            arr.processingType = this.processingType;
            arr.xml = this.xml;
            return arr;
        }
    });
    
    return ResponseProcessing;
});