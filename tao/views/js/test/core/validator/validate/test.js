define(['lodash', 'core/validator/Validator'], function(_, Validator){

    var CL = console.log, _test = function(){};
    
    test('simple validate', function(){
        
        var validator = new Validator(['notEmpty', 'numeric']);

        equal(_.size(validator.rules), 2, 'rules set');

        validator.validate('a', function(res){
            
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'success');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'failure');
            equal(report2.data.validator, 'numeric');
        });
        
        validator.validate('', function(res){
            
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'failure');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'failure');
            equal(report2.data.validator, 'numeric');
        });
        
        validator.validate(0, function(res){
            
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'success');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'success');
            equal(report2.data.validator, 'numeric');
        });
        
        validator.validate(3, function(res){
            
            equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'success');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'success');
            equal(report2.data.validator, 'numeric');
        });

    });

    test('validate with validator options', function(){
        
        var validator = new Validator([
            {
                name : 'pattern',
                options : {
                    pattern : '[A-Z][a-z]{3,}',
                    modifier : 'igm'
                }
            }
        ]);
        
        validator.validate('York', function(res){
            
            equal(_.size(res), 1, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'success');
            equal(report1.data.validator, 'pattern');
        });
        
        validator.validate('Aee', function(res){
            equal(res.shift().type, 'failure');
        });
        
    });
    
    test('validate with validating options', function(){
        
        var validator = new Validator(['notEmpty', 'numeric', 'qtiIdentifier']);

        equal(_.size(validator.rules), 3, 'rules set');
        
        //empty options
        validator.validate('', {}, function(res){
            
            equal(_.size(res), 3, 'validated');

            var report1 = res.shift();
            equal(report1.type, 'failure');
            equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            equal(report2.type, 'failure');
            equal(report2.data.validator, 'numeric');

            var report3 = res.shift();
            equal(report3.type, 'failure');
            equal(report3.data.validator, 'qtiIdentifier');
        });
        
        //test lazy option : stop on first failure
        validator.validate('', {lazy:true}, function(res){
            
            equal(_.size(res), 1, 'validated');
            var report1 = res.shift();
            equal(report1.type, 'failure');
            equal(report1.data.validator, 'notEmpty');

        });
        
    });

});
