define(['taoQtiItem/qtiItem/core/interactions/BlockInteraction', 'taoQtiItem/qtiItem/core/Object'], function(QtiBlockInteraction, QtiObject){
    var QtiObjectInteraction = QtiBlockInteraction.extend({
        //common methods to object containers (start)
        initObject : function(object){
            this.object = object || new QtiObject();
        },
        getObject : function(){
            return this.object;
        }
        //common methods to object containers (end)
    });
    
    return QtiObjectInteraction;
});

