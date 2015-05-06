define(['lodash'], function(_){
    
    var _updateChoiceIdentifierInResponse = function(response, oldId, newId){

        var escapedOldId = oldId.replace(/([.-])/g, '\\$1'),
            regex = new RegExp('\\b(' + escapedOldId + ')\\b');//@todo: to be tested in presence of special chars

        for(var i in response.correctResponse){
            response.correctResponse[i] = response.correctResponse[i].replace(regex, newId);
        }

        var mapEntries = {};
        _.forIn(response.mapEntries, function(value, mapKey){
            mapKey = mapKey.replace(regex, newId);
            mapEntries[mapKey] = value;
        });
        response.mapEntries = mapEntries;
    };

    var _updateChoiceIdentifier = _.throttle(function(choice, newId){

        var oldId = choice.id();

        if(oldId !== newId){
            //need to update correct response and mapping values too !
            var response = choice.getInteraction().getResponseDeclaration();
            _updateChoiceIdentifierInResponse(response, oldId, newId);

            //finally, set the new identifier to the choice
            choice.id(newId);
        }
    }, 300);
    
    var methods = {
        id:function(value){
            _updateChoiceIdentifier(this, value);
        }
    };

    return methods;
});