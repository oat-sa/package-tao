define(['taoQtiItem/qtiItem/mixin/Mixin', 'lodash'], function(Mixin, _){

    var methods = {
        getNamespace : function(){

            if(this.ns && this.ns.name && this.ns.uri){
                return _.clone(this.ns);
            }else{

                var relatedItem = this.getRelatedItem();
                if(relatedItem){
                    var namespaces = relatedItem.getNamespaces();
                    for(var ns in namespaces){
                        if(namespaces[ns].indexOf(this.nsUriFragment) > 0){
                            return {
                                name : ns,
                                uri : namespaces[ns]
                            };
                        }
                    }
                    //if no ns found in the item, set the default one!
                    relatedItem.namespaces[this.defaultNsName] = this.defaultNsUri;
                    return {
                        name : this.defaultNsName,
                        uri : this.defaultNsUri
                    };
                }
            }

            return {};
        },
        setNamespace : function(name, uri){
            this.ns = {
                name : name,
                uri : uri
            };
        }
    };

    return {
        augment : function(targetClass){
            Mixin.augment(targetClass, methods);
        },
        methods : methods
    };
});