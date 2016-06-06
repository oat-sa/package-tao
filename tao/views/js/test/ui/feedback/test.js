define(['jquery', 'ui/feedback'], function($, feedback){
        
    QUnit.module('feedback');
   
    QUnit.test('module', function(assert){
        QUnit.expect(1);

        assert.ok(typeof feedback === 'function', 'The module expose a function');
    });

    QUnit.test('api', function(assert){
        QUnit.expect(10);

        var fb = feedback();

        assert.ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        assert.ok(typeof fb._container === 'object'            , 'The feedback instance has a _container member');
        assert.ok(typeof fb._container.selector === 'string'   , 'The _container is a jquery object');
        assert.ok(typeof fb.message === 'function'             , 'The feedback instance has a message method');
        assert.ok(typeof fb.info === 'function'                , 'The feedback instance has a info method');
        assert.ok(typeof fb.success === 'function'             , 'The feedback instance has a success method');
        assert.ok(typeof fb.warning === 'function'             , 'The feedback instance has a warning method');
        assert.ok(typeof fb.error === 'function'               , 'The feedback instance has an error method');
        assert.ok(typeof fb.open === 'function'                , 'The feedback instance has an open method');
        assert.ok(typeof fb.close === 'function'               , 'The feedback instance has a close method');
    });

    QUnit.test('factory', function(assert){
        QUnit.expect(3);

        var fb1 = feedback();
        var fb2 = feedback();

        assert.ok(typeof fb1 === 'object'                       , 'The feedback function creates an object');
        assert.ok(typeof fb2 === 'object'                       , 'The feedback function creates an object');
        notStrictEqual(fb1, fb2                          , 'The feedback function creates object instances');
    });

    QUnit.test('wrong container', function(assert){
        QUnit.expect(1);

        assert.throws(function(){

            feedback( $('#foofoo'));

        }, Error, 'An exception should be thrown if the container is not an existing element');
    });
    
    QUnit.test('state', function(assert){
        QUnit.expect(9);
        
        var fb = feedback().message();
        var fb2 = feedback().message();

        assert.ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        assert.ok(typeof fb._state === 'string'                , 'The feedback contains has a _state member');
        assert.ok(typeof fb.setState === 'function'            , 'The feedback instance has a setState method');
        assert.ok(typeof fb.isInState === 'function'           , 'The feedback instance has an isInState method');
        assert.equal(fb._state, 'created'                      , 'The feedback instance starts with the created state');
        assert.ok(fb.isInState('created')                      , 'The isInState method verify the currrent state');
        assert.equal(fb2._state, 'created'                      , 'The 2nd feedback instance starts with the created state');

        fb2.setState('closed');

        assert.equal(fb2._state, 'closed'                      , 'Once changed the current state is changed');
        assert.equal(fb._state, 'created'                      , 'Once changed it does not interfer with other instances');
    });

    QUnit.test('default message', function(assert){
        QUnit.expect(5);
        
        var fb = feedback();
        var r2 = fb.message();

        assert.ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        strictEqual(fb, r2                              , 'The message function is fluent');
        assert.ok(typeof fb.content === 'string'               , 'The content property has been created');
        assert.equal(fb.category, 'volatile'                   , 'The category of info is volatile');
        assert.ok(/feedback-info/m.test(fb.content)            , 'The content property contains the right css class');
    });

    QUnit.test('parameterized message', function(assert){
        QUnit.expect(5);

        var fb = feedback().message('success', 'AWESOME_MESSAGE');

        assert.ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        assert.ok(typeof fb.content === 'string'               , 'The content property has been created');
        assert.equal(fb.level, 'success'                       , 'The level is set to success');
        assert.ok(/feedback-success/m.test(fb.content)         , 'The content property contains the right css class');
        assert.ok(/AWESOME_MESSAGE/m.test(fb.content)          , 'The content property contains the message');
    });

    QUnit.test('display message', function(assert){
        QUnit.expect(3);
        var $container = $('#feedback-box');

        var fb = feedback($container).message('warning', 'DANGER_ZONE').display();

        assert.equal(fb.level, 'warning'                               , 'The level is set to warning');
        assert.ok(/DANGER_ZONE/m.test(fb.content)                      , 'The content property contains the message');
        assert.equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');
    });
    
    QUnit.test('close message', function(assert){
        QUnit.expect(2);

        var $container = $('#feedback-box');
        var fb = feedback($container).message('warning', 'DANGER_ZONE').display();

        assert.equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');

        fb.close();
        assert.equal($('.feedback-warning', $container).length, 0, 'The feedback content has been removed from the container');
    });
    
    QUnit.asyncTest('close event', function(assert){
    
        QUnit.expect(2);

        var $container = $('#feedback-box');
        var fb = feedback($container).message('warning', 'DANGER_ZONE').display();

        assert.equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');

        $container.on('close.feedback', function(e){
            assert.equal($('.feedback-warning', $container).length, 0, 'The feedback content has been removed from the container');
            QUnit.start();
        });

        fb.close();
    });

    QUnit.asyncTest('close button', function(assert) {

        QUnit.expect(2);

        var $container = $('#feedback-box');
        feedback($container).message('warning', 'DANGER_ZONE').display();

        assert.equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');

        $container.on('close.feedback', function(e){
            assert.equal($('.feedback-warning', $container).length, 0, 'The feedback content has been removed from the container');
            QUnit.start();
        });
        
        $container.find('.icon-close').click();
    });
    
    QUnit.asyncTest('callbacks', function(assert){
    
        QUnit.expect(3);

        var $container = $('#feedback-box');
        feedback($container)
            .message('warning', 'DANGER_ZONE', {
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

    QUnit.asyncTest('timeout', function(assert){
    
        QUnit.expect(4);

        var $container = $('#feedback-box');
        var fb = feedback($container).message('info', 'AWESOME_MESSAGE', { timeout : 10 }).display();

        assert.equal($('.feedback-info', $container).length, 1 , 'The feedback content has been appended to the container');

        $container.on('close.feedback', function(e){
            assert.equal($('.feedback-info', $container).length, 0, 'The feedback content has been removed from the container');
            QUnit.start();
        });

        var fbt = feedback($container.clone()).message('info', 'AWESOME_MESSAGE').display();
        assert.notEqual(fbt._getTimeout(), fbt._getTimeout('error'), 'Different feedback types have different timeouts by default');
        assert.notEqual(fb._getTimeout(), fbt._getTimeout(), 'It\'s possible to set custom timeout for message');
        fbt.close();

    });
    
    QUnit.test('volatile messages', function(assert){
    
        QUnit.expect(2);

        var $container = $('#feedback-box');
        var fb1 = feedback($container).message('info', 'AWESOME_MESSAGE_1').open();
        var fb2 = feedback($container).message('info', 'AWESOME_MESSAGE_2').open();

        assert.equal($('.feedback-info', $container).length, 1 , 'The container has only one volatile message');
        assert.ok(/AWESOME_MESSAGE_2/.test($('.feedback-info div', $container).text()), 'The container has the 2nd message');
    });

});


