define(['taoItems/runtime/ItemServiceImpl'], function(ItemServiceImpl){
    
    function QTIItemService(){
        ItemServiceImpl.apply(this, arguments);
    }
    QTIItemService.prototype = ItemServiceImpl.prototype;
    
    
    return QTIItemService;
});