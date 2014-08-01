define(
['lodash', 'core/encoder/boolean', 'core/encoder/number', 'core/encoder/time', 'core/encoder/array2str', 'core/encoder/str2array'], 
function(_, boolean, number, time, array2str, str2array){
    
    /**
     * Extract the argument in parenthesis from a function name:  "foo(a,b)" return [a,b]
     * @param {string} name - the declaration : array(a,b)
     * @returns {array} of extracted args 
     */
    var extractArgs = function extractArgs(name){
        var args = [];
        var matches = []; 
        if(name.indexOf('(') > -1){
            matches = /\((.+?)\)/.exec(name);
            if(matches && matches.length >= 1){
                args = matches[1].split(',');
            }
        }
        return args;
    };
    
    /**
     * Extract the name from a function declaration:   "foo(a,b)" return foo
     * @param {string} name - the declaration : foo(a,b)
     * @returns {string} the name
     */
    var extractName = function extractName(name){
        if(name.indexOf('(') > -1){
            return name.substr(0, name.indexOf('('));
        }
        return name;
    };
    
   /** 
    * Provides multi sources encoding decoding
    * @exports core/encoder/encoders
    */
    var Encoders =  {
        number : number,
        time : time,
        boolean : boolean,
        array2str : array2str,
        str2array : str2array,
        
        register : function(name, encode, decode){
            if(!_.isString(name)){
                throw new Error('An encoder must have a valid name');
            }
            if(!_.isFunction(encode)){
                throw new Error('Encode must be a function');
            }
            if(!_.isFunction(decode)){
                throw new Error('Decode must be a function');
            }
            this[name] = { encode : encode, decode : decode };
        },
        
        encode : function(name, value){
            name = extractName(name);
            if(this[name]){
                var encoder = this[name];
                var args = [value];
                return encoder.encode.apply(encoder, args.concat(extractArgs(name)));
            }
            return value;
        },
        
        decode : function(name, value){
            name = extractName(name);
            if(this[name]){
                var decoder = this[name];
                var args = [value];
                return decoder.decode.apply(decoder, args.concat(extractArgs(name)));
            }
            return value;
        }
    };
    
    return Encoders;
});

