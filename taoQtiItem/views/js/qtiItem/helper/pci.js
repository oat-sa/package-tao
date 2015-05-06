define(['lodash'], function(_){

    var pci = {
        getRawValues : function(pciVar){
            if(_.isPlainObject(pciVar)){
                if(pciVar.base!== undefined){
                    return _.values(pciVar.base);
                }else if(pciVar.list){
                    return _.values(pciVar.list);
                }
            }
            throw 'unsupported type ';
        }
    };

    return pci;
});