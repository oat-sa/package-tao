define(['core/format'], function(format){
    'use strict';

    test('protoype', function(){
       ok(typeof format === 'function', 'Format is a function');
    });

    test('formating', function(){
        equal(format('give me a %s', 'string'), 'give me a string' , 'Format with a string replacement');
        equal(format('give me two %s %s', 'awesome', 'strings'), 'give me two awesome strings' , 'Format with 2 string replacements');
        
        equal(format('give me an %d', 11), 'give me an 11' , 'Format with 1 an int');
        equal(format('give me an %d', '11'), 'give me an 11' , 'Format with 1 an string as number');
        equal(format('give me an %d', 11.5), 'give me an 11.5' , 'Format with 1 a float');
        equal(format('give me an %d', '11.5'), 'give me an 11.5' , 'Format with a float in a string');
        equal(format('give me %d%', 100), 'give me 100%' , 'Format with percent edge case');
    
        equal(format('give me a %j', 100), 'give me a 100' , 'Format with a json number');
        equal(format('give me a %j', 'kiss'), 'give me a kiss' , 'Format with a json string');
        equal(format('give me an %j', ['A', 'rr', 'ay']), "give me an [A,rr,ay]" , 'Format with a json array');
        equal(format('give me %j', { a : 1, b : true, c : null, d : 'test'}), 'give me {a:1,b:true,c:null,d:test}' , 'Format with a json object');
    });
});
