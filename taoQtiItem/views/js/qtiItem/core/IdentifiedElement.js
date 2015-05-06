define(['taoQtiItem/qtiItem/core/Element'], function(Element){
    var IdentifiedElement = Element.extend({
        buildIdentifier : function(prefix, useSuffix){
            if(useSuffix === undefined){
                useSuffix = true;
            }
            var item = this.getRelatedItem();
            if(item){
                var suffix = '', i = 1, usedIds = item.getUsedIdentifiers();
                if(prefix){
                    prefix = prefix.replace(/_[0-9]+$/ig, '_'); //detect incremental id of type choice_12, response_3, etc.
                    prefix = prefix.replace(/[^a-zA-Z0-9_]/ig, '_');
                    prefix = prefix.replace(/(_)+/ig, '_');
                    if(useSuffix){
                        suffix = '_' + i;
                    }
                }else{
                    prefix = this.qtiClass;
                    suffix = '_' + i;
                }

                do{
                    var exists = false;
                    var id = prefix + suffix;
                    if(usedIds[id]){
                        exists = true;
                        suffix = '_' + i;
                        i++;
                    }
                }while(exists);

                this.attr('identifier', id);
            }else{
                throw 'cannot build identifier of an element that is not attached to an assessment item';
            }
            return this;
        },
        id : function(value){
            if(!value && !this.attr('identifier')){
                this.buildIdentifier(value);
            }
            return this.attr('identifier', value);
        }
    });

    return IdentifiedElement;
});

