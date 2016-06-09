/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['lodash'], function(_){
    'use strict';

    var pattern = /(%[sdj])/g;

    /**
     * Enables you to format strings/message, using the pattern:
     *  - %s : string
     *  - %d : number
     *  - %j : json
     *
     * @example format('Resize %s to %d%', 'width', 100); //returns Resize width to 100%
     * @exports core/format
     * @param {String} message - the message to format
     * @param {...String|Number|Object} [replacements] -  the replacements arguments in the order defined in the message
     * @returns {String} the formatted message
     */
    return function(message){
        var replacements = Array.prototype.slice.call(arguments, 1);
        return _.reduce(
            message.match(pattern),
            function(acc, match, index){
                var replacement = '';
                if(undefined !== replacements[index]){
                     switch(match){
                        case '%d': replacement = Number(replacements[index]); break;
                        case '%j': try{
                                replacement = JSON.stringify(replacements[index]).replace(/"/g, '');
                            } catch(e){}
                            break;
                        default : replacement = replacements[index]; break;
                     }
                     message = message.replace(match, replacement);
                }
                return message;
            },
            message
        );
    };
});
