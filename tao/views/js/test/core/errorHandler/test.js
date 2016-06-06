define(['core/errorHandler'], function(errorHandler){


    QUnit.test('module API', 5, function(assert){
        assert.ok(typeof errorHandler === 'object', "The errorHandler module exposes an object");
        assert.ok(typeof errorHandler.getContext === 'function', "The errorHandler has a method getContext");
        assert.ok(typeof errorHandler.listen === 'function', "The errorHandler has a method listen");
        assert.ok(typeof errorHandler.throw === 'function', "The errorHandler has a method throw");
        assert.ok(typeof errorHandler.reset === 'function', "The errorHandler has a method reset");
    });

    QUnit.module('Error', {
        teardown : function(){
            errorHandler._contexts = {};
        }
    });

    QUnit.test('create a context', 3, function(assert){

        assert.ok(typeof errorHandler._contexts.context1 === 'undefined', 'The context is not yet created');

        errorHandler.listen('context1');

        assert.ok(typeof errorHandler._contexts.context1 === 'object', 'The context has been created');
        assert.ok(typeof errorHandler.getContext('context1') === 'object', 'The context is available');

    });

    QUnit.test('reset a context', 3, function(assert){

        assert.ok(typeof errorHandler._contexts.context1 === 'undefined', 'The context is not yet created');

        errorHandler.listen('context1');

        assert.ok(typeof errorHandler._contexts.context1 === 'object', 'The context has been created');

        errorHandler.reset('context1');

        assert.ok(typeof errorHandler._contexts.context1 === 'undefined', 'The context is now removed');
    });

    QUnit.asyncTest('throw an error', 2, function(assert){

        errorHandler.listen('footext', function(err){

            assert.ok(err instanceof Error, 'we got an Error');
            assert.equal(err.message, 'foo', 'the error is the one thrown');

            QUnit.start();
        });

        errorHandler.throw('footext', new Error('foo'));
    });

     QUnit.asyncTest('throw a string error', 2, function(assert){

        errorHandler.listen('footext', function(err){

            assert.ok(err instanceof Error, 'we got an Error');
            assert.equal(err.message, 'foo', 'the error is the one thrown');

            QUnit.start();
        });

        errorHandler.throw('footext', 'foo');
    });


    QUnit.asyncTest('listen typed errors', 4, function(assert){

        errorHandler.listen('footext', 'TypeError', function(err){

            assert.ok(err instanceof Error, 'we got an Error');
            assert.ok(err instanceof TypeError, 'we got a TypeError');
            assert.equal(err.name, 'TypeError', 'the error is the one thrown');
            assert.equal(err.message, 'bar', 'the error is the one thrown');

            QUnit.start();
        });

        errorHandler.throw('footext', new Error('foo'));
        errorHandler.throw('footext', new TypeError('bar'));
    });


    QUnit.asyncTest('listen global and typed errors', 8, function(assert){

        errorHandler.listen('footext', 'TypeError', function(err){
            assert.ok(err instanceof Error, 'we got an Error');
            assert.ok(err instanceof TypeError, 'we got a TypeError');
            assert.equal(err.name, 'TypeError', 'the error is the one thrown');
            assert.equal(err.message, 'bar', 'the error is the one thrown');
        });

        errorHandler.listen('footext', function(err){

            assert.ok(err instanceof Error, 'we got an Error');
            assert.ok(err instanceof TypeError, 'we got a TypeError');
            assert.equal(err.name, 'TypeError', 'the error is the one thrown');
            assert.equal(err.message, 'bar', 'the error is the one thrown');

            QUnit.start();
        });
        errorHandler.throw('footext', new TypeError('bar'));
    });


});
