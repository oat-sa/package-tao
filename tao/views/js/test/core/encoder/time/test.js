define(['jquery', 'core/encoder/time'], function($, TimeEnc){
   
    QUnit.test('encode', function(assert){
        QUnit.expect(3);
        
        assert.ok(typeof TimeEnc.encode === 'function');
        
        assert.equal(TimeEnc.encode(5400), '01:30:00');
        assert.equal(TimeEnc.encode(30), '00:00:30');
    });
    
    QUnit.test('decode', function(assert){
        QUnit.expect(3);
        
         assert.ok(typeof TimeEnc.decode === 'function');
        
         assert.equal(TimeEnc.decode('01:30:00'), 5400);
         assert.equal(TimeEnc.decode('00:00:30'), 30);
    });
});


