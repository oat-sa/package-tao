define(['jquery', 'ui/lock'], function($, lock){
    'use strict';

    QUnit.module('lock');

    QUnit.test('module', function(assert){
        QUnit.expect(1);

        assert.ok(typeof lock === 'function', 'The module expose a function');
    });

    QUnit.test('api', function(assert){
        QUnit.expect(11);

        var lk = lock();
        assert.ok(typeof lk === 'object'                       , 'The lock function creates an object');
        assert.ok(typeof lk._container === 'object'            , 'The lock instance has a _container member');
        assert.ok(typeof lk._container.selector === 'string'   , 'The _container is a jquery object');
        assert.ok(typeof lk.message === 'function'             , 'The lock instance has a message method');
        assert.ok(typeof lk.hasLock === 'function'             , 'The lock instance has a hasLock method');
        assert.ok(typeof lk.locked === 'function'              , 'The lock instance has a locked method');
        assert.ok(typeof lk.open === 'function'                , 'The lock instance has a open method');
        assert.ok(typeof lk.display === 'function'             , 'The lock instance has a display method');
        assert.ok(typeof lk.close === 'function'               , 'The lock instance has a close method');
        assert.ok(typeof lk.release === 'function'             , 'The lock instance has a release method');
        assert.ok(typeof lk.register === 'function'            , 'The lock instance has a register method');
    });

    QUnit.test('factory', function(assert){
        QUnit.expect(3);

        var lock1 = lock();
        var lock2 = lock();
        assert.ok(typeof lock1 === 'object'                       , 'The lock function creates an object');
        assert.ok(typeof lock2 === 'object'                       , 'The lock function creates an object');
        assert.notStrictEqual(lock1, lock2                        , 'The lock function creates object instances');
    });

    QUnit.test('wrong container', function(assert){
        QUnit.expect(1);

        assert.throws(function(){

            lock( $('#foofoo'));

        }, Error, 'An exception should be thrown if the container is not an existing element');
    });

    QUnit.test('state', function(assert){
        QUnit.expect(11);

        var lk = lock().message();
        var lock2 = lock().message();

        assert.ok(typeof lk === 'object'                       , 'The lock function creates an object');
        assert.ok(typeof lk._state === 'string'                , 'The lock contains a _state member');
        assert.ok(typeof lk.setState === 'function'            , 'The lock instance has a setState method');
        assert.ok(typeof lk.isInState === 'function'           , 'The lock instance has an isInState method');
        assert.equal(lk._state, 'created'                      , 'The lock instance starts with the created state');
        assert.ok(lk.isInState('created')                      , 'The isInState method verify the current state');
        assert.ok(lk.isInState(['created'])                      , 'The isInState method verify the current state');
        assert.equal(lock2._state, 'created'                   , 'The 2nd lock instance starts with the created state');

        lock2.setState('closed');

        assert.equal(lock2._state, 'closed'                      , 'Once changed the current state is changed');
        assert.equal(lk._state, 'created'                        , 'Once changed it does not interfere with other instances');
        assert.throws(function(){
            lock().setState('notAState');
        },Error, 'State doesn\'t exist so it throws an error');

    });

    QUnit.test('register', function(assert){
        QUnit.expect(6);

        var lk = lock($('#alt-lock-box')).register();

        assert.ok(typeof lk === 'object'                       , 'The lock function creates an object');
        assert.ok(typeof lk.content === 'string'               , 'The content property has been created');
        assert.equal(lk.category, 'hasLock'                    , 'The category of info is hasLock');
        assert.equal(lk.options.uri, '123'                     , 'The uri is correctly read from the div');
        assert.ok(/test-msg/m.test(lk.content)                 , 'The content property contains the message');
        assert.ok(/feedback-info/m.test(lk.content)            , 'The content property contains the right css class');
    });

    QUnit.test('default message', function(assert){
        QUnit.expect(5);

        var lk = lock();
        var r2 = lk.message();

        assert.ok(typeof lk === 'object'                       , 'The lock function creates an object');
        assert.strictEqual(lk, r2                              , 'The message function is fluent');
        assert.ok(typeof lk.content === 'string'               , 'The content property has been created');
        assert.equal(lk.category, 'hasLock'                    , 'The category of info is hasLock');
        assert.ok(/feedback-info/m.test(lk.content)            , 'The content property contains the right css class');
    });

    QUnit.test('parameterized message', function(assert){
        QUnit.expect(5);
        var lk = lock().locked('AWESOME_MESSAGE');

        assert.ok(typeof lk === 'object'                       , 'The lock function creates an object');
        assert.ok(typeof lk.content === 'string'               , 'The content property has been created');
        assert.equal(lk.level, 'error'                         , 'The level is set to error');
        assert.ok(/feedback-error/m.test(lk.content)           , 'The content property contains the right css class');
        assert.ok(/AWESOME_MESSAGE/m.test(lk.content)          , 'The content property contains the message');
    });

    QUnit.test('display message', function(assert){
        QUnit.expect(3);
        var $container = $('#lock-box');

        var lk = lock($container).hasLock('LOCKED_RESOURCE');

        assert.equal(lk.level, 'info'                               , 'The level is set to info');
        assert.ok(/LOCKED_RESOURCE/m.test(lk.content)               , 'The content property contains the message');
        assert.equal($('.feedback-info', $container).length, 1      , 'The lock content has been appended to the container');
    });

    QUnit.test('close message', function(assert){
        QUnit.expect(2);

        var $container = $('#lock-box');
        var lk = lock($container).message('locked', 'LOCKED_RESOURCE').display();

        assert.equal($('.feedback-error', $container).length, 1 , 'The lock content has been appended to the container');

        lk.close();
        assert.equal($('.feedback-error', $container).length, 0, 'The lock content has been removed from the container');
    });

    QUnit.asyncTest('close event', function(assert){

        QUnit.expect(2);

        var $container = $('#lock-box');
        var lk = lock($container).message('hasLock', 'LOCKED_RESOURCE').display();

        assert.equal($('.feedback-info', $container).length, 1 , 'The lock content has been appended to the container');

        $container.on('close.lock', function(e){
            assert.equal($('.feedback-info', $container).length, 0, 'The lock content has been removed from the container');
            QUnit.start();
        });

        lk.close();
    });

    QUnit.asyncTest('release', function(assert){

        QUnit.expect(4);

        var $container = $('#lock-box');

        lock($container)
            .hasLock('LOCKED_RESOURCE', {
                failed : function(){
                    assert.ok(true, 'The release failed and callback is called');
                },
                uri: 123,
                releaseUrl : ''
            })
            .release()
            .close();

        lock($container)
            .hasLock('LOCKED_RESOURCE', {
                failed : function(){
                    assert.ok(true, 'The release failed and callback is called');
                },
                uri: 123,
                releaseUrl : 'error.json'
            })
            .release()
            .close();

        lock($container)
            .hasLock('LOCKED_RESOURCE', {
                failed : function(){
                    assert.ok(true, 'The release failed and callback is called');
                },
                uri: 123,
                releaseUrl : 'wrong.json'
            })
            .release()
            .close();

        setTimeout(function(){
            lock($container)
            .hasLock('LOCKED_RESOURCE', {
                released : function(){
                    assert.ok(true, 'The release works and callback is called');
                    QUnit.start();
                },
                uri: 123,
                releaseUrl : 'success.json'
            })
            .release()
            .close();
        }, 200);
    });

    QUnit.asyncTest('callbacks', function(assert){

        QUnit.expect(3);

        var $container = $('#lock-box');
        lock($container)
            .message('locked', 'LOCKED_RESOURCE', {
                create : function(){
                    assert.ok(true, 'The create callback is called');
                },
                display : function(){
                    assert.ok(true, 'The close callback is called');
                },
                close : function(){
                    assert.ok(true, 'The close callback is called');
                    QUnit.start();
                }
            })
            .display()
            .close();
    });

});


