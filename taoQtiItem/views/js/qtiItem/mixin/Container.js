define(['taoQtiItem/qtiItem/mixin/Mixin', 'taoQtiItem/qtiItem/core/Container'], function(Mixin, Container){

    var methods = {
        initContainer : function(body){
            this.bdy = new Container(body || '');
            this.bdy.setRelatedItem(this.getRelatedItem() || null);
            this.bdy.contentModel = 'blockStatic';
        },
        getBody : function(){
            return this.bdy;
        },
        body : function(body){
            var ret = this.bdy.body(body);
            return (body) ? this : ret;//for method chaining on get
        },
        setElement : function(element, body){
            this.bdy.setElement(element, body);
            return this;
        },
        removeElement : function(element){
            return this.bdy.removeElement(element);
        },
        getElements : function(qtiClass){
            return this.bdy.getElements(qtiClass);
        },
        getElement : function(serial){
            return this.bdy.getElement(serial);
        }
    };

    return {
        augment : function(targetClass){
            Mixin.augment(targetClass, methods);
        },
        methods : methods
    };
});