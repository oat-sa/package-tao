define(['taoQtiItem/qtiItem/core/choices/Choice', 'taoQtiItem/qtiItem/core/Object'], function(QtiChoice, QtiObject){
    var QtiGapImg = QtiChoice.extend({
        qtiClass : 'gapImg',
        //common methods to object containers (start)
        initObject : function(object){
            this.object = object || new QtiObject();
        },
        getObject : function(){
            return this.object;
        }
        //common methods to object containers (end)
    });
    return QtiGapImg;
});


