define(['lodash'], function(_){
    return {
        removeChoice : function(response, choice){

            var escapedIdentifier = choice.id().replace(/([.-])/g, '\\$1'),
                regex = new RegExp('([^a-z_\-\d\.]*)(' + escapedIdentifier + ')([^a-z_\-\d\.]*)');

            for(var i in response.correctResponse){
                if(response.correctResponse[i].match(regex)){
                    delete response.correctResponse[i];
                }
            }

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