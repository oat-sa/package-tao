define(['taoQtiItem/qtiItem/core/IdentifiedElement'], function(IdentifiedElement){
    var Feedback = IdentifiedElement.extend({
        is : function(qtiClass){
            return (qtiClass === 'feedback') || this._super(qtiClass);
        }
    });
    return Feedback;
});