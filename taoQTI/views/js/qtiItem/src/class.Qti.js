//namespace declaration:
Qti = {};
Qti.traits = {
    container : {
        initContainer : function(body){
            this.bdy = new Qti.Container(body || '');
            this.bdy.setRelatedItem(this.getRelatedItem() || null);
        },
        getBody : function(){
            return this.bdy;
        },
        body : function(body){
            return this.bdy.body(body);
        }
    },
    object : {
        initObject : function(object){
            this.object = object || new Qti.Object();
        },
        getObject : function(){
            return this.object;
        }
    }
};