define(['core/logger/console'], function(consoleLogger){
    'use strict';

    var cinfo = window.console.info;
    var clog = window.console.log;
    var cdebug = window.console.debug;
    var ctrace = window.console.trace;

    QUnit.test("api", function(assert){
        QUnit.expect(3);

        assert.ok(typeof consoleLogger !== 'undefined', "The module exports something");
        assert.ok(typeof consoleLogger === 'object', "The module exposes an object");
        assert.equal(typeof consoleLogger.log, 'function', 'The logger has a log method');
    });

    QUnit.module('console logger', {
        teardown : function(){
             window.console.info = cinfo;
             window.console.log = clog;
             window.console.debug = cdebug;
             window.console.trace = ctrace;
        }
    });

    QUnit.asyncTest("info log", function(assert){
        QUnit.expect(1);

        //hack the window...
        window.console.info = function(message){
           assert.equal(message, 'foo', 'The message match');
           QUnit.start();
        };

        consoleLogger.log({
            level: 'info',
            messages : ['foo']
        });
    });

    QUnit.asyncTest("fatal log", function(assert){
        QUnit.expect(2);

        //hack the window...
        window.console.log = function(level, message){
           assert.equal(level, 'FATAL', 'The level falls back to messages');
           assert.equal(message, 'baz', 'The message match');
           QUnit.start();
        };

        consoleLogger.log({
            level: 'fatal',
            messages : ['baz']
        });
    });

    QUnit.asyncTest("trace multiple messages", function(assert){
        QUnit.expect(3);

        //hack the window...
        window.console.trace = function(message1, message2, message3){
           assert.equal(message1, 'foo', 'The message match');
           assert.equal(message2, 'bar', 'The message match');
           assert.equal(message3, 'baz', 'The message match');
           QUnit.start();
        };

        consoleLogger.log({
            level: 'trace',
            messages : ['foo', 'bar', 'baz']
        });
    });

    QUnit.asyncTest("debug withe context", function(assert){
        QUnit.expect(2);

        //hack the window...
        window.console.trace = function(context, message){
           assert.equal(context, '[context]', 'The context match');
           assert.equal(message, 'abar', 'The message match');
           QUnit.start();
        };

        consoleLogger.log({
            level: 'trace',
            messages : ['abar'],
            context : 'context'
        });
    });
});



