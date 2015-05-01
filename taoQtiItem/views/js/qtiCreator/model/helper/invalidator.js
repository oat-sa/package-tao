define(['lodash'], function(_){
    "use strict";
    var invalidator = {
        completelyValid : function(element){
            
            var item = element.getRelatedItem();
            var serial, invalidElements;
            if(item){
                serial = element.getSerial();
                invalidElements = item.data('invalid') || {};
            
                delete invalidElements[serial];
                item.data('invalid', invalidElements);
            }
        },
        valid : function(element, key){

            var item = element.getRelatedItem();
            var serial = element.getSerial();
            var invalidElements;

            if(item){
                invalidElements = item.data('invalid') || {};

                if(key){

                    if(invalidElements[serial] && invalidElements[serial][key]){
                        delete invalidElements[serial][key];
                        if(!_.size(invalidElements[serial])){
                            delete invalidElements[serial];
                        }

                        item.data('invalid', invalidElements);
                    }

                }else{
                    throw new Error('missing required argument "key"');
                }
            }
        },
        invalid : function(element, key, message, stateName){

            var item = element.getRelatedItem();
            var serial = element.getSerial();
            var invalidElements;

            if(item){
                invalidElements = item.data('invalid') || {};

                if(key){

                    if(!invalidElements[serial]){
                        invalidElements[serial] = {};
                    }

                    invalidElements[serial][key] = {
                        message : message || '',
                        stateName : stateName || 'active'
                    };
                    item.data('invalid', invalidElements);

                }else{
                    throw new Error('missing required arguments "key"');
                }
            }
        },
        isValid : function(element){

            var item = element.getRelatedItem();
            var serial = element.getSerial();
            var invalidElements;

            if(item){
                invalidElements = item.data('invalid') || {};
                return !invalidElements[serial];
            }
            return true;
        }
    };

    return invalidator;
});


