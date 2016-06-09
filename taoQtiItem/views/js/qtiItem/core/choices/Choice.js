define(['taoQtiItem/qtiItem/core/IdentifiedElement'], function(IdentifiedElement){

    var Choice = IdentifiedElement.extend({
        init : function(serial, attributes){
            this._super(serial, attributes);
        },
        is : function(qtiClass){
            return (qtiClass === 'choice') || this._super(qtiClass);
        },
        getInteraction : function(){
            var found, ret = null, item = this.getRelatedItem();
            if(item){
                found = item.find(this.serial);
                if(found){
                    ret = found.parent;
                }
            }
            return ret;
        }
    });

    return Choice;
});