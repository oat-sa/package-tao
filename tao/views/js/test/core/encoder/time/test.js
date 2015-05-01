define(['jquery', 'core/encoder/time'], function($, TimeEnc){
   
    test('encode', function(){
        expect(3);
        
        ok(typeof TimeEnc.encode === 'function');
        
        equal(TimeEnc.encode(5400), '01:30:00');
        equal(TimeEnc.encode(30), '00:00:30');
    });
    
    test('decode', function(){
        expect(3);
        
         ok(typeof TimeEnc.decode === 'function');
        
         equal(TimeEnc.decode('01:30:00'), 5400);
         equal(TimeEnc.decode('00:00:30'), 30);
    });
});


