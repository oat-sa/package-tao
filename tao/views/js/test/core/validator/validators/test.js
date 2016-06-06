define([
    'core/validator/validators'
], function(validators){
    'use strict';

    QUnit.module('API');

    QUnit.test('module', function(assert){
        QUnit.expect(3);
        assert.ok(typeof validators === 'object', 'The validators module exports an object');
        assert.ok(typeof validators.register === 'function', 'The validators object has a register method');
        assert.ok(typeof validators.validators === 'object', 'The validators object has a validators property');
    });


    QUnit.module('register');


    QUnit.test('validtor validation', function(assert){
        QUnit.expect(5);

        assert.throws(function(){
             validators.register();
        }, Error, 'Registering a validator needs a name and a validator');

        assert.throws(function(){
             validators.register('', { message: 'foo', validate : function(){}});
        }, Error, 'Registering a validator needs a valid name');

        assert.throws(function(){
             validators.register({ message: 'foo', validate : function(){}});
        }, Error, 'Registering a validator needs a valid name');

        assert.throws(function(){
             validators.register('foo');
        }, Error, 'Registering a validator object');

        assert.throws(function(){
             validators.register('foo', {});
        }, Error, 'Registering a validator object with a message and a validate function');
    });


    QUnit.test('name based validtor register', function(assert){
        QUnit.expect(4);

        assert.ok(typeof validators.validators.foo === 'undefined', 'The registered validator is not yet available');

        validators.register('foo', {
            message : 'foo',
            validate : function(){
                return true;
            }
        });

        assert.ok(typeof validators.validators.foo === 'object', 'The registered validator is available');
        assert.equal(validators.validators.foo.message, 'foo', 'The registered validator has the right message');
        assert.ok(typeof validators.validators.foo.validate === 'function', 'The registered validator has a validate method');
    });

    QUnit.test('object based validtor register', function(assert){
        QUnit.expect(4);

        assert.ok(typeof validators.validators.bar === 'undefined', 'The registered validator is not yet available');

        validators.register({
            name : 'bar',
            message : 'bar',
            validate : function(){
                return true;
            }
        });

        assert.ok(typeof validators.validators.bar === 'object', 'The registered validator is available');
        assert.equal(validators.validators.bar.message, 'bar', 'The registered validator has the right message');
        assert.ok(typeof validators.validators.bar.validate === 'function', 'The registered validator has a validate method');
    });

    QUnit.test('register only once by default', function(assert){
        QUnit.expect(7);

        assert.ok(typeof validators.validators.moo === 'undefined', 'The registered validator is not yet available');

        validators.register('moo', {
            message : 'moo',
            validate : function(){
                return true;
            }
        });

        assert.ok(typeof validators.validators.moo === 'object', 'The registered validator is available');
        assert.equal(validators.validators.moo.message, 'moo', 'The registered validator has the right message');
        assert.ok(typeof validators.validators.moo.validate === 'function', 'The registered validator has a validate method');

        validators.register('moo', {
            message : 'woof',
            validate : function(){
                return true;
            }
        });
        assert.ok(typeof validators.validators.moo === 'object', 'The registered validator is available');
        assert.notEqual(validators.validators.moo.message, 'woof', 'The registered validator has not the new message');
        assert.equal(validators.validators.moo.message, 'moo', 'The registered validator has not the first message');

    });

    QUnit.test('force validtor register', function(assert){
        QUnit.expect(6);

        assert.ok(typeof validators.validators.baz === 'undefined', 'The registered validator is not yet available');

        validators.register('baz', {
            message : 'baz',
            validate : function(){
                return true;
            }
        });

        assert.ok(typeof validators.validators.baz === 'object', 'The registered validator is available');
        assert.equal(validators.validators.baz.message, 'baz', 'The registered validator has the right message');
        assert.ok(typeof validators.validators.baz.validate === 'function', 'The registered validator has a validate method');

        validators.register('baz', {
            message : 'noz',
            validate : function(){
                return true;
            }
        }, true);
        assert.ok(typeof validators.validators.baz === 'object', 'The registered validator is available');
        assert.equal(validators.validators.baz.message, 'noz', 'The registered validator has the new message');

    });
});

