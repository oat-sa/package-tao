function IdentifierList(list, updateCallback){
    this.list = list || {};
    this.onUpdate = (typeof updateCallback === 'function') ? updateCallback : function(){
    };
}

IdentifierList.prototype.set = function(serial, identifier){
    if(this.list[serial]){
        //update:
        this.list[serial] = identifier;
        this.onUpdate(serial, identifier);
    }else{
        this.list[serial] = identifier;
    }
};

IdentifierList.prototype.unset = function(serial){
    delete this.list[serial];
};

IdentifierList.prototype.exists = function(identifier){
    var ret = false;
    for(var i in this.list){
        if(this.list[i] === identifier){
            ret = i;
        }
    }
    return ret;
};
