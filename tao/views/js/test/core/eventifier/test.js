define(['core/eventifier'], function(eventifier){
    'use strict';

    QUnit.module('eventifier');

    QUnit.test("api", 2, function(assert){
        assert.ok(typeof eventifier !== 'undefined', "The module exports something");
        assert.ok(typeof eventifier === 'function', "The module has an eventifier method");
    });


    QUnit.module('eventification');

    QUnit.test("delegates", 4, function(assert){

        var emitter = eventifier();

        assert.ok(typeof emitter === 'object', "the emitter definition is an object");
        assert.ok(typeof emitter.on === 'function', "the emitter defintion holds the method on");
        assert.ok(typeof emitter.trigger === 'function', "the emitter defintion holds the method trigger");
        assert.ok(typeof emitter.off === 'function', "the emitter defintion holds the method off");
    });

    QUnit.asyncTest("listen and trigger with params", 3, function(assert){

        var emitter = eventifier();
        var params = ['bar', 'baz'];

        emitter.on('foo', function handleFoo(p0, p1){
            assert.ok(true, "The foo event is triggered on emitter");
            assert.equal(p0, params[0], 'The received parameters are those from the trigger');
            assert.equal(p1, params[1], 'The received parameters are those from the trigger');
            QUnit.start();
        });

        emitter.trigger('foo', params[0], params[1]);
    });

    QUnit.test("on context", 1, function(assert){

        var emitter1 = eventifier();
        var emitter2 = eventifier();

        assert.notDeepEqual(emitter1, emitter2, "Emitters are different objects");
    });


    QUnit.asyncTest("trigger context", 2, function(assert){
        var emitter1 = eventifier();
        var emitter2 = eventifier();

        emitter1.on('foo', function(success){
            assert.ok(success, "The foo event is triggered on emitter1");
        });
        emitter2.on('foo', function(success){
            assert.ok(success, "The foo event is triggered on emitter2");
            QUnit.start();
        });

        emitter1.trigger('foo', true);
        setTimeout(function(){
            emitter2.trigger('foo', true);
        }, 10);
    });

    QUnit.asyncTest("off", 1, function(assert){
        var emitter = eventifier();

        emitter.on('foo', function(){
            assert.ok(false, "The foo event shouldn't be triggered");
        });
        emitter.on('bar', function(){
            assert.ok(true, "The bar event should  be triggered");
            QUnit.start();
        });

        emitter.off('foo');
        emitter.trigger('foo');
        setTimeout(function(){
            emitter.trigger('bar');
        }, 10);
    });

    QUnit.asyncTest("multiple listeners", 2, function(assert){
        var emitter = eventifier();

        emitter.on('foo', function(){
            assert.ok(true, "The 1st foo listener should be executed");
        });
        emitter.on('foo', function(){
            assert.ok(true, "The 2nd foo listener should be executed");
            QUnit.start();
        });

        emitter.trigger('foo');
    });

    QUnit.module('namspaces');

    QUnit.asyncTest("listen namespace, trigger without namespace", function(assert){
        QUnit.expect(2);

        var emitter = eventifier();

        emitter.on('foo', function(){
            assert.ok(true, 'the foo handler is called');
        });
        emitter.on('foo.bar', function(){
            assert.ok(true, 'the foo.bar handler is called');
            QUnit.start();
        });

        emitter.trigger('foo');
    });

    QUnit.asyncTest("listen namespace, trigger with namespace", function(assert){
        QUnit.expect(1);

        var emitter = eventifier();

        emitter.on('foo', function(){
            assert.ok(false, 'the foo handler should not be called');
        });
        emitter.on('foo.bar', function(){
            assert.ok(true, 'the foo.bar handler is called');
            QUnit.start();
        });

        emitter.trigger('foo.bar');
    });

    QUnit.asyncTest("off namespaced event", function(assert){
        QUnit.expect(0);

        var emitter = eventifier();

        emitter.on('foo', function(){
            assert.ok(false, 'the foo handler should not be called');
        });
        emitter.on('foo.bar', function(){
            assert.ok(false, 'the foo.bar handler should not be called');
        });
        emitter.off('foo');

        emitter.trigger('foo');
        setTimeout(function(){
            QUnit.start();
        }, 1);
    });

    QUnit.asyncTest("off all namespace", function(assert){
        QUnit.expect(1);

        var emitter = eventifier();

        emitter.on('foo', function(){
            assert.ok(true, 'the foo handler should  be called');
            QUnit.start();
        });
        emitter.on('foo.bar', function(){
            assert.ok(false, 'the foo.bar handler should not be called');

        });
        emitter.on('norz.bar', function(){
            assert.ok(false, 'the norz.bar handler should not be called');
        });

        emitter.off('.bar');

        emitter.trigger('foo').trigger('norz');

    });

    QUnit.module('before');

    QUnit.asyncTest("sync done - return nothing", function(assert){

        var testDriver = eventifier();
        var arg1 = 'X',
            arg2 = 'Y';

        testDriver.on('next', function(){
            assert.ok(true, "The 1st listener should be executed : e.g. save context recovery");
        });
        testDriver.on('next', function(){
            assert.ok(true, "The 2nd listener should be executed : e.g. save resposne ");
        });
        testDriver.on('next', function(){
            assert.ok(true, "The third and last listener should be executed : e.g. move to next item");
            QUnit.start();
        });

        testDriver.before('next', function(e, a1, a2){
            assert.equal(a1, arg1, 'the first event arg is correct');
            assert.equal(a2, arg2, 'the second event arg is correct');
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate item state");
        });
        testDriver.before('next', function(e, a1, a2){
            assert.equal(a1, arg1, 'the first event arg is correct');
            assert.equal(a2, arg2, 'the second event arg is correct');
            assert.ok(true, "The 2nd 'before' listener should be executed : e.g. validate a special interaction state");
        });

        testDriver.trigger('next', arg1, arg2);
    });

    QUnit.asyncTest("async done", function(assert){

        var testDriver = eventifier();
        var arg1 = 'X',
            arg2 = 'Y';

        testDriver.on('next', function(){
            assert.ok(true, "The 1st listener should be executed : e.g. save context recovery");
        });
        testDriver.on('next', function(){
            assert.ok(true, "The 2nd listener should be executed : e.g. save resposne ");
        });
        testDriver.on('next', function(){
            assert.ok(true, "The third and last listener should be executed : e.g. move to next item");
            QUnit.start();
        });

        testDriver.before('next', function(e, a1, a2){
            assert.equal(a1, arg1, 'the first event arg is correct');
            assert.equal(a2, arg2, 'the second event arg is correct');
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate item state");
            var done = e.done();
            setTimeout(function(){
                done();
            }, 10);
        });

        testDriver.before('next', function(e, a1, a2){
            assert.equal(a1, arg1, 'the first event arg is correct');
            assert.equal(a2, arg2, 'the second event arg is correct');
            assert.ok(true, "The 2nd 'before' listener should be executed : e.g. validate a special interaction state");
        });

        testDriver.trigger('next', arg1, arg2);
    });

    QUnit.test("async done - fail to call done()", 1, function(assert){

        var testDriver = eventifier();

        testDriver.on('next', function(){
            assert.ok(true, "The listener should not be executed : e.g. save context recovery");
        });

        testDriver.before('next', function(e){
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate item state");
            var done = e.done();
            //fail to call done here although we are in an async context
        });

        testDriver.before('next', function(e){
            assert.ok(true, "The 2nd 'before' listener should not be executed : e.g. validate a special interaction state");
        });

        testDriver.trigger('next');
    });

    QUnit.asyncTest("sync prevent - return false", function(assert){

        var itemEditor = eventifier();

        itemEditor.on('save', function(){
            assert.ok(true, "The listener should not be executed : e.g. do save item");
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate current edition form");
            //form invalid
            return false;
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 2nd 'before' listener should be executed : e.g. do save item stylesheet");
            QUnit.start();
        });

        itemEditor.trigger('save');
    });

    QUnit.asyncTest("sync prevent - call prevent()", function(assert){

        var itemEditor = eventifier();

        itemEditor.on('save', function(){
            assert.ok(true, "The listener should not be executed : e.g. do save item");
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate current edition form");
            //form invalid
            e.prevent();
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 2nd 'before' listener should be executed : e.g. do save item stylesheet");
            QUnit.start();
        });

        itemEditor.trigger('save');
    });

    QUnit.asyncTest("async prevent", function(assert){

        var itemEditor = eventifier();

        itemEditor.on('save', function(){
            assert.ok(true, "The listener should not be executed : e.g. do save item");
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate current edition form");
            var done = e.done();
            setTimeout(function(){
                e.prevent();
            }, 10);
            //form invalid
            return false;
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 2nd 'before' listener should be executed : e.g. do save item stylesheet");
            QUnit.start();
        });

        itemEditor.trigger('save');
    });

    QUnit.asyncTest("sync prevent now", 1, function(assert){

        var itemEditor = eventifier();

        itemEditor.on('save', function(){
            assert.ok(true, "The listener should not be executed : e.g. do save item");
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate current edition form");
            //form invalid that interrupt all following call
            e.preventNow();
            QUnit.start();
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 2nd 'before' listener should not be executed : e.g. do save item stylesheet");
        });

        itemEditor.trigger('save');
    });


    QUnit.asyncTest("async prevent now", 1, function(assert){

        var itemEditor = eventifier();

        itemEditor.on('save', function(){
            assert.ok(true, "The listener should not be executed : e.g. do save item");
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 1st 'before' listener should be executed : e.g. validate current edition form");
            e.done();

            //form invalid that interrupt all following call
            setTimeout(function(){
                e.preventNow();
            }, 10);

            QUnit.start();
        });
        itemEditor.before('save', function(e){
            assert.ok(true, "The 2nd 'before' listener should not be executed : e.g. do save item stylesheet");
        });

        itemEditor.trigger('save');
    });

    QUnit.asyncTest("namespaced events before order", function(assert){
        QUnit.expect(12);

        var emitter = eventifier();
        var state = {
            foo : false,
            foobar : false,
            beforefoo: false,
            beforefoobar : false
        };

        emitter.on('foo', function(){
            assert.ok(true, "The foo handler is called");
            assert.equal(state.beforefoo, true, 'The before foo handler should hoave been called');
            assert.equal(state.beforefoobar, true, 'The before foo.bar handler should have been called');
            state.foo = true;
        });
        emitter.on('foo.bar', function(){
            assert.ok(true, "The foo.bar handler is called");
            assert.equal(state.beforefoo, true, 'The before foo handler should have been called');
            assert.equal(state.beforefoobar, true, 'The before foo.bar handler should have been called');
            state.foobar = true;
        });
        emitter.before('foo', function(){
            assert.ok(true, "The after foo handler is called");
            assert.equal(state.foo, false, 'The foo handler should not have been called');
            assert.equal(state.foobar, false, 'The foo.bar handler should have been called');
            state.beforefoo = true;
        });
        emitter.before('foo.bar', function(){
            assert.ok(true, "The after foo.bar handler is called");
            assert.equal(state.foo, false, 'The foo handler should not have been called');
            assert.equal(state.foobar, false, 'The foo.bar handler should have been called');
            state.beforefoobar = true;
        });

        emitter.trigger('foo');

        setTimeout(function(){
            QUnit.start();
        }, 10);
    });


    QUnit.module('after');

    QUnit.asyncTest("trigger", 2, function(assert){

        var testDriver = eventifier();

        testDriver.on('next', function(){
            assert.ok(true, "This listener should be executed : e.g. move to next item");
        });

        testDriver.after('next', function(){
            assert.ok(true, "This listener should be executed : e.g. push response to storage");
            QUnit.start();
        });

        testDriver.trigger('next');
    });

    QUnit.asyncTest("namespaced after events order", function(assert){
        QUnit.expect(12);

        var emitter = eventifier();
        var state = {
            foo : false,
            foobar : false,
            afterfoo: false,
            afterfoobar : false
        };

        emitter.on('foo', function(){
            assert.ok(true, "The foo handler is called");
            assert.equal(state.afterfoo, false, 'The after foo handler should not be called yet');
            assert.equal(state.afterfoobar, false, 'The after foo.bar handler should not be called yet');
            state.foo = true;
        });
        emitter.on('foo.bar', function(){
            assert.ok(true, "The foo.bar handler is called");
            assert.equal(state.afterfoo, false, 'The after foo handler should not be called yet');
            assert.equal(state.afterfoobar, false, 'The after foo.bar handler should not be called yet');
            state.foobar = true;
        });
        emitter.after('foo', function(){
            assert.ok(true, "The after foo handler is called");
            assert.equal(state.foo, true, 'The foo handler should have been called');
            assert.equal(state.foobar, true, 'The foo.bar handler should have been called');
            state.afterfoo = true;
        });
        emitter.after('foo.bar', function(){
            assert.ok(true, "The after foo.bar handler is called");
            assert.equal(state.foo, true, 'The foo handler should have been called');
            assert.equal(state.foobar, true, 'The foo.bar handler should have been called');
            state.afterfoobar = true;
        });

        emitter.trigger('foo');

        setTimeout(function(){
            QUnit.start();
        }, 10);
    });


    QUnit.module('multiple events names');

    QUnit.asyncTest("listen multiples, trigger one by one", function(assert){
        QUnit.expect(2);

        var emitter = eventifier();

        var counter = 0;
        emitter.on('foo bar', function(){
            assert.ok(true, 'the handler is called');

            if(++counter === 2){
                QUnit.start();
            }
        });
        emitter.trigger('foo')
               .trigger('bar');
    });

    QUnit.asyncTest("listen multiple, trigger multiples with params", function(assert){
        QUnit.expect(8);

        var emitter = eventifier();

        var counter = 0;
        emitter.on('foo bar', function(bool, str, num){
            assert.ok(true, 'the handler is called');
            assert.equal(bool, true, 'The 1st parameter is correct');
            assert.equal(str, 'yo', 'The 2nd parameter is correct');
            assert.equal(num, 1.4, 'The 3rd parameter is correct');

            if(++counter === 2){
                QUnit.start();
            }
        });
        emitter.trigger('foo bar', true, 'yo', 1.4);
    });

    QUnit.asyncTest("listen multiple, off multiple", function(assert){
        QUnit.expect(1);

        var emitter = eventifier();

        emitter.on('foo bar', function(){
            assert.ok(false, 'the handler must not be called');
        });
        emitter.off('foo bar');

        emitter.trigger('foo')
               .trigger('bar');

        setTimeout(function(){
            assert.ok(true, 'control');
            QUnit.start();
        }, 10);
    });

    QUnit.asyncTest("support namespace in multiple events", function(assert){
        QUnit.expect(3);

        var emitter = eventifier();

        var counter = 0;
        emitter.on('foo bar.moo', function(){
            assert.ok(true, 'the handler is called');

            if(++counter === 3){
                QUnit.start();
            }
        });

        emitter.trigger('foo bar.moo bar');
    });

    QUnit.module('logger');

    QUnit.asyncTest("logging events", 2, function(assert){
        QUnit.expect(3);

        var emitter = eventifier({
                name : 'moo'
            }, {
            debug : function (name, method, eventName){
                assert.equal(name, 'moo', 'The logger get the target name');
                assert.equal(method, 'trigger', 'The logger get the method name');
                assert.equal(eventName, 'foo.bar', 'The logger get the event name');

                QUnit.start();
            }
        });

        emitter.trigger('foo.bar');
    });

});
