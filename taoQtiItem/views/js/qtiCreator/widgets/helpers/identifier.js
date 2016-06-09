define(['lodash'], function(_){

    var _updateChoiceIdentifierInResponse = function(response, oldId, newId){
        
        var escapedOldId = oldId.replace(/([.-])/g, '\\$1'),//escape spec characters allowed in the standard and that is meaningful in regex
            regex = new RegExp('([^\\s]*\\s+|^)(' + escapedOldId + ')(\\s+[^\\s]*|$)');//prepare the regex to watch the oldId to be replaced

        for(var i in response.correctResponse){
            response.correctResponse[i] = response.correctResponse[i].replace(regex, '$1'+newId+'$3');
        }

        var mapEntries = {};
        _.forIn(response.mapEntries, function(value, mapKey){
            mapKey = mapKey.replace(regex, '$1'+newId+'$3');
            mapEntries[mapKey] = value;
        });
        response.mapEntries = mapEntries;
    };

    var _updateChoiceIdentifier = _.throttle(function(choice, newId, response){
        
        var oldId = choice.id();
        if(oldId !== newId){
            //need to update correct response and mapping values too !
            _updateChoiceIdentifierInResponse(response, oldId, newId);

            //finally, set the new identifier to the choice
            choice.id(newId);

            //update domi
            var interaction = _get('interactionFromChoice', choice, function(){
                return choice.getInteraction();
            });
                
            //FIXME some choices may not have a renderer, so we catch the thrown exception silently
            try{
                var $choiceContainer = choice.getContainer(null, choice.qtiClass+'.'+interaction.qtiClass);
                $choiceContainer.attr('data-identifier', choice.id());
                $choiceContainer.find('input').val(choice.id());
            } catch(ex){}

        }
    }, 500);


    var _cache = [];

    var _setCache = function(cache, element, value){
        if(!_cache[cache]){
            _cache[cache] = {};
        }
        _cache[cache][element.getSerial()] = value;
    };

    var _getCache = function(cache, element){
        var serial = element.getSerial();
        if(serial && _cache[cache] && _cache[cache][serial]){
            return _cache[cache][serial];
        }
        return null;
    };

    var _get = function(cache, element, callback){
        var ret = _getCache(cache, element);
        if(!ret){
            ret = callback.call(element);
            _setCache(cache, element, ret);
        }
        return ret;
    };

    return {
        updateChoiceIdentifier : function(choice, value){
            
            value = value.trim();
            if(value){
                var response = _get('responseFromChoice', choice, function(){
                    var interaction = _get('interactionFromChoice', choice, function(){
                        return choice.getInteraction();
                    });
                    return interaction.getResponseDeclaration();
                });

                _updateChoiceIdentifier(choice, value, response);
            }
        },
        updateResponseIdentifier : function(response, value){
            throw 'to be implemented';
        }
    };

});


