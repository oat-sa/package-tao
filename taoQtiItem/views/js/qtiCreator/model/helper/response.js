define(['lodash'], function(_){
    "use strict";
    return {
        removeChoice : function(response, choice){

            var escapedIdentifier = choice.id().replace(/([.-])/g, '\\$1'),
                regex = new RegExp('([^a-z_\-\d\.]*)(' + escapedIdentifier + ')([^a-z_\-\d\.]*)');
            
            _.remove(response.correctResponse, function(entry){
                return entry.match(regex);
            });
            
            var mapEntries = {};
            _.forIn(response.mapEntries, function(value, mapKey){
                if(!mapKey.match(regex)){
                    mapEntries[mapKey] = value;
                }
            });
            response.mapEntries = mapEntries;

        }
    };
});