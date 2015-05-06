define(['lodash', 'jquery', 'ui/validator'], function(_, $) {

    test('create, destroy', function(){
        
        $('#text1').validator();
        ok($('#text1').validator('getValidator'), 'validator bound');
        ok($('#text1').data('validator-instance'), 'validator bound');
        ok($('#text1').data('validator-config'), 'validator bound');
        
        $('#text1').validator('destroy');
        ok(!$('#text1').data('validator-instance'), 'validator bound');
        ok(!$('#text1').data('validator-config'), 'validator bound');
    });
    
    test('validate empty validator', function(){
        
        expect(1);
        $('#text0').validator('validate', function(valid, res){
            console.log(res);
            ok(valid, 'valid empty validator');
        });
        
        
    });
        
    test('validate element', function(){

        //set test value;

        $('#text1').validator();
        ok($('#text1').validator('getValidator'), 'validator bound');

        stop();
        $('#text1').val('York');
        $('#text1').validator('validate', {}, function(valid, res) {
            start();

            ok(valid, 'the element is valid');
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'success');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'success');
            equal(report2.data.validator, 'pattern');
        });


        stop();
        $('#text1').val('');
        $('#text1').validator('validate', {}, function(valid, res) {
            start();

            ok(valid === false, "the element isn't valid");
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'failure');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'failure');
            equal(report2.data.validator, 'pattern');
        });

        stop();
        $('#text1').val('Yor');
        $('#text1').validator('validate', {}, function(valid, res) {
            start();

            ok(valid === false, "the element isn't valid");
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'success');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'failure');
            equal(report2.data.validator, 'pattern');
        });

        //reset test value:
        $('#text1').val('');
        $('#text1').validator('destroy');
    });

    test('element event', function() {

        stop();
        $('#text1').on('validated', function(e, data) {
            start();
            equal(e.type, 'validated', 'event type ok');
            equal(data.elt, this, 'validated element ok');
            equal(_.size(data.results), 2, 'results ok');
        });
        $('#text1').validator('validate');
        
        $('#text1').validator('destroy').off('validated');
    });

    test('form event', function() {

        stop();
        $('#form1').on('validated', function(e, data) {
            start();
            equal(e.type, 'validated', 'event type ok');
            equal(data.elt, $('#text1')[0], 'validated element ok');
            equal(_.size(data.results), 2, 'results ok');
        });
        $('#text1').validator('validate');
        
        $('#text1').validator('destroy');
        $('#form1').off('validated');
    });
    
    test('callback and event binding', function(){
        
        expect(5);
        
        //set validators and options in data attributes:
        $('#text2').data('validate', '$notEmpty; $pattern(pattern=[A-Z][a-z]{5,})');
        $('#text2').data('validate-option', '$lazy; $event(type=keyup, length=3);');
        
        //set default callback, and test to results
        $('#text2').validator({
            validated:function(valid, results){
                equal(this, $('#text2')[0], 'validated element ok');
                equal(_.size(results), 2, 'results ok');
            }
        });
        
        //set additonal event "validated" listener and test to results
        $('#text2').on('validated', function(e, data){
            equal(e.type, 'validated', 'event type ok');
            equal(data.elt, $('#text2')[0], 'validated element ok');
            equal(_.size(data.results), 2, 'results ok');
        });
        
        //set text and validation according to event "keyup" option, then trigger it:
        $('#text2').val('Abcdef').keyup();
    });
    
});
