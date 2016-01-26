define(['lodash', 'core/validator/Validator'], function(_, Validator){
    'use strict';

    QUnit.test('simple validate', function(assert){

        var validator = new Validator(['notEmpty', 'numeric']);

        assert.equal(_.size(validator.rules), 2, 'rules set');

        validator.validate('a', function(res){

            assert.equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            assert.equal(report1.type, 'success');
            assert.equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            assert.equal(report2.type, 'failure');
            assert.equal(report2.data.validator, 'numeric');
        });

        validator.validate('', function(res){

            assert.equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            assert.equal(report1.type, 'failure');
            assert.equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            assert.equal(report2.type, 'failure');
            assert.equal(report2.data.validator, 'numeric');
        });

        validator.validate(0, function(res){

            assert.equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            assert.equal(report1.type, 'success');
            assert.equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            assert.equal(report2.type, 'success');
            assert.equal(report2.data.validator, 'numeric');
        });

        validator.validate(3, function(res){

            assert.equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            assert.equal(report1.type, 'success');
            assert.equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            assert.equal(report2.type, 'success');
            assert.equal(report2.data.validator, 'numeric');
        });

    });

    QUnit.test('validate with validator options', function(assert){

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

            assert.equal(_.size(res), 1, 'validated');

            var report1 = res.shift();
            assert.equal(report1.type, 'success');
            assert.equal(report1.data.validator, 'pattern');
        });

        validator.validate('Aee', function(res){
            assert.equal(res.shift().type, 'failure');
        });

    });

    QUnit.test('validate with validating options', function(assert){

        var validator = new Validator(['notEmpty', 'numeric']);

        assert.equal(_.size(validator.rules), 2, 'rules set');

        //empty options
        validator.validate('', {}, function(res){

            assert.equal(_.size(res), 2, 'validated');

            var report1 = res.shift();
            assert.equal(report1.type, 'failure');
            assert.equal(report1.data.validator, 'notEmpty');

            var report2 = res.shift();
            assert.equal(report2.type, 'failure');
            assert.equal(report2.data.validator, 'numeric');
        });

        //test lazy option : stop on first failure
        validator.validate('', {lazy:true}, function(res){

            assert.equal(_.size(res), 1, 'validated');
            var report1 = res.shift();
            assert.equal(report1.type, 'failure');
            assert.equal(report1.data.validator, 'notEmpty');

        });

    });

});
