define(['jquery', 'ui/feedback'], function($, feedback){
        
    module('feedback');
   
    test('module', function(){
        expect(1);

        ok(typeof feedback === 'function', 'The module expose a function');
    });

    test('api', function(){
        expect(10);

        var fb = feedback();

        ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        ok(typeof fb._container === 'object'            , 'The feedback instance has a _container member');
        ok(typeof fb._container.selector === 'string'   , 'The _container is a jquery object');
        ok(typeof fb.message === 'function'             , 'The feedback instance has a message method');
        ok(typeof fb.info === 'function'                , 'The feedback instance has a info method');
        ok(typeof fb.success === 'function'             , 'The feedback instance has a success method');
        ok(typeof fb.warning === 'function'             , 'The feedback instance has a warning method');
        ok(typeof fb.error === 'function'               , 'The feedback instance has an error method');
        ok(typeof fb.open === 'function'                , 'The feedback instance has an open method');
        ok(typeof fb.close === 'function'               , 'The feedback instance has a close method');
    });

    test('factory', function(){
        expect(3);

        var fb1 = feedback();
        var fb2 = feedback();

        ok(typeof fb1 === 'object'                       , 'The feedback function creates an object');
        ok(typeof fb2 === 'object'                       , 'The feedback function creates an object');
        notStrictEqual(fb1, fb2                          , 'The feedback function creates object instances');
    });

    test('wrong container', function(){
        expect(1);

        throws(function(){

            feedback( $('#foofoo'));

        }, Error, 'An exception should be thrown if the container is not an existing element');
    });
    
    test('state', function(){
        expect(9);
        
        var fb = feedback().message();
        var fb2 = feedback().message();

        ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        ok(typeof fb._state === 'string'                , 'The feedback contains has a _state member');
        ok(typeof fb.setState === 'function'            , 'The feedback instance has a setState method');
        ok(typeof fb.isInState === 'function'           , 'The feedback instance has an isInState method');
        equal(fb._state, 'created'                      , 'The feedback instance starts with the created state');
        ok(fb.isInState('created')                      , 'The isInState method verify the currrent state');
        equal(fb2._state, 'created'                      , 'The 2nd feedback instance starts with the created state');

        fb2.setState('closed');

        equal(fb2._state, 'closed'                      , 'Once changed the current state is changed');
        equal(fb._state, 'created'                      , 'Once changed it does not interfer with other instances');
    });

    test('default message', function(){
        expect(5);
        
        var fb = feedback();
        var r2 = fb.message();

        ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        strictEqual(fb, r2                              , 'The message function is fluent');
        ok(typeof fb.content === 'string'               , 'The content property has been created');
        equal(fb.category, 'volatile'                   , 'The category of info is volatile');
        ok(/feedback-info/m.test(fb.content)            , 'The content property contains the right css class');
    });

    test('parameterized message', function(){
        expect(5);

        var fb = feedback().message('success', 'AWESOME_MESSAGE');

        ok(typeof fb === 'object'                       , 'The feedback function creates an object');
        ok(typeof fb.content === 'string'               , 'The content property has been created');
        equal(fb.level, 'success'                       , 'The level is set to success');
        ok(/feedback-success/m.test(fb.content)         , 'The content property contains the right css class');
        ok(/AWESOME_MESSAGE/m.test(fb.content)          , 'The content property contains the message');
    });

    test('display message', function(){
        expect(3);
        var $container = $('#feedback-box');

        var fb = feedback($container).message('warning', 'DANGER_ZONE').display();

        equal(fb.level, 'warning'                               , 'The level is set to warning');
        ok(/DANGER_ZONE/m.test(fb.content)                      , 'The content property contains the message');
        equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');
    });
    
    test('close message', function(){
        expect(2);

        var $container = $('#feedback-box');
        var fb = feedback($container).message('warning', 'DANGER_ZONE').display();

        equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');

        fb.close();
        equal($('.feedback-warning', $container).length, 0, 'The feedback content has been removed from the container');
    });
    
    asyncTest('close event', function(){
    
        expect(2);

        var $container = $('#feedback-box');
        var fb = feedback($container).message('warning', 'DANGER_ZONE').display();

        equal($('.feedback-warning', $container).length, 1 , 'The feedback content has been appended to the container');

        $container.on('close.feedback', function(e){
            equal($('.feedback-warning', $container).length, 0, 'The feedback content has been removed from the container');
            start();
        });

        fb.close();
    });
    
    asyncTest('callbacks', function(){
    
        expect(3);

        var $container = $('#feedback-box');
        feedback($container)
            .message('warning', 'DANGER_ZONE', {
                create : function(){
                    ok(true, 'The create callback is called');
                },
                display : function(){
                    ok(true, 'The close callback is called');
                },
                close : function(){
                    ok(true, 'The close callback is called');
                    start();
                } 
            })
            .display()
            .close();
    });

    asyncTest('timeout', function(){
    
        expect(2);

        var $container = $('#feedback-box');
        var fb = feedback($container).message('info', 'AWESOME_MESSAGE', { timeout : 10 }).display();

        equal($('.feedback-info', $container).length, 1 , 'The feedback content has been appended to the container');

        $container.on('close.feedback', function(e){
            equal($('.feedback-info', $container).length, 0, 'The feedback content has been removed from the container');
            start();
        });
    });
    
    test('volatile messages', function(){
    
        expect(2);

        var $container = $('#feedback-box');
        var fb1 = feedback($container).message('info', 'AWESOME_MESSAGE_1').open();
        var fb2 = feedback($container).message('info', 'AWESOME_MESSAGE_2').open();

        equal($('.feedback-info', $container).length, 1 , 'The container has only one volatile message');
        ok(/AWESOME_MESSAGE_2/.test($('.feedback-info div', $container).text()), 'The container has the 2nd message');
    });

});


