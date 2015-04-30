define(['jquery', 'lodash', 'taoItems/runner/api/itemRunner', 'taoItems/test/runner/provider/dummyProvider'], function($, _, itemRunner, dummyProvider){


    QUnit.module('API');

    QUnit.test('module', function(assert){
        assert.ok(typeof itemRunner !== 'undefined', "The module exports something");
        assert.ok(typeof itemRunner === 'function', "The module exports a function");
        assert.ok(typeof itemRunner.register === 'function', "The function has a property function register");
    });


// itemRunner.register

    QUnit.module('Register a Provider', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.test('Error without provider', function(assert){
        QUnit.expect(1);

        assert.throws(function(){
            itemRunner();
        }, Error, 'An error is thrown');
    });

    QUnit.test('Error with a wrong provider', function(assert){
        QUnit.expect(3);

        assert.throws(function(){
            itemRunner.register('');
        }, TypeError, 'A name is expected');

        assert.throws(function(){
            itemRunner.register('testProvider');
        }, TypeError, 'A objet is expected');

        assert.throws(function(){
            itemRunner.register('testProvider', {});
        }, TypeError, 'At least init or render method is expected');
    });

    QUnit.test('Register a minimal provider', function(assert){
        QUnit.expect(4);

        assert.ok(typeof itemRunner.providers === 'undefined', "the itemRunner comes without a provider");

        itemRunner.register('testProvider', {
            init : function(){
            }
        });
        itemRunner();

        assert.ok(typeof itemRunner.providers === 'object', 'The providers property is defined');
        assert.ok(typeof itemRunner.providers.testProvider === 'object', 'The testProvider is set');
        assert.ok(typeof itemRunner.providers.testProvider.init === 'function', 'The testProvider has an init function');
    });


// itemRunner().init()

    module('ItemRunner init', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Initialize the runner', function(assert){
        QUnit.expect(4);

        assert.throws(function(){
            itemRunner('dummyProvider', {});
        }, Error, 'An error is thrown when no provider is set');

        itemRunner.register('dummyProvider', dummyProvider);

        assert.throws(function(){
            itemRunner('zoommyProvider', {});
        }, Error, 'An error is thrown when requesting the wrong provider');

        itemRunner('dummyProvider', {
            type: 'number'
        }).on('init', function(){

            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'number', 'the itemRunner context got the right data assigned');

            QUnit.start();
        }).init();
    });

    QUnit.asyncTest('Get the default provider', function(assert){
        QUnit.expect(2);

        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner({
            type: 'number'
        }).on('init', function(){

            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'number', 'the itemRunner context got the right data assigned');

            QUnit.start();
        }).init();
    });

    QUnit.asyncTest('Initialize the item with new data', function(assert){
        QUnit.expect(2);

        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner('dummyProvider', {
            type: 'number'
        }).on('init', function(){

            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'text', 'the itemRunner context got the right data assigned');

            QUnit.start();
        }).init({
            type : 'text'
        });
    });


    QUnit.asyncTest('No init in the provider', function(assert){
        QUnit.expect(1);

        itemRunner.register('dummyProvider', _.omit(dummyProvider, 'init'));

        var runner = itemRunner('dummyProvider', {
            type: 'search'
        }).on('init', function(){

            assert.ok(true, 'init is still called');

            QUnit.start();
        })
        .init();
    });


// itemRunner().render()

    module('ItemRunner render', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Render an item from an HTMLElement', function(assert){
        QUnit.expect(5);

        var container = document.getElementById('item-container');
        assert.equal(container.id, 'item-container', 'the item container exists');
        assert.equal(container.childNodes.length, 0, 'the container has no children');

        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner('dummyProvider', {
            type: 'number'
        }).on('render', function(){

            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'number', 'the itemRunner context got the right data assigned');

            assert.equal(container.childNodes.length, 1, 'the container has now children');

            QUnit.start();
        })
        .init()
        .render(container);
    });

    QUnit.asyncTest('Render an item from a jQueryElement', function(assert){
        QUnit.expect(4);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($('input', $container).length, 0, 'the container does not contains an input');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'search'
        }).on('render', function(){
            var $input = $('input', $container);
            assert.equal($input.length, 1, 'the container contains an input');
            assert.equal($input.attr('type'), 'search', 'the input has the right type');

            QUnit.start();
        })
        .init()
        .render($container);
    });

    QUnit.asyncTest('Render an item into wrong element', function(assert){
        QUnit.expect(2);

        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner('dummyProvider', {
            type: 'search'
        }).on('error', function(message){
            assert.ok(typeof message === 'string', 'An error message is given');
            assert.ok(message.length > 0 , 'A non empty message is given');
            QUnit.start();
        })
        .init()
        .render("item-container");
    });

    QUnit.asyncTest('Render an item without element', function(assert){
        QUnit.expect(2);

        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner('dummyProvider', {
            type: 'search'
        }).on('error', function(message){
            assert.ok(typeof message === 'string', 'An error message is given');
            assert.ok(message.length > 0 , 'A non empty message is given');
            QUnit.start();
        })
        .init()
        .render();
    });

    QUnit.asyncTest('No clear in the provider', function(assert){
        QUnit.expect(1);

        var $container = $('#item-container');

        itemRunner.register('dummyProvider', _.omit(dummyProvider, 'render'));

        var runner = itemRunner('dummyProvider', {
            type: 'search'
        }).on('render', function(){

            assert.ok(true, 'render is still called');

            QUnit.start();
        })
        .init()
        .render($container);
    });

    QUnit.asyncTest('Render sync with init async', function(assert){
        QUnit.expect(2);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', _.defaults({
            init: function(data, done){
                var self = this;
                setTimeout(function(){
                    self._data = data;
                    done();
                }, 100);
            }
        }, dummyProvider));

        itemRunner('dummyProvider', {
            type: 'text'
        }).on('render', function(){
            assert.ok(true, 'Rendered done');
            QUnit.start();
        })
        .init()
        .render($container);
    });


// itemRunner().clear()

    module('ItemRunner clear', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Clear a rendered element', function(assert){
        QUnit.expect(4);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'search'
        }).on('render', function(){
            assert.equal($container.children().length, 1, 'the container has a child');

            this.clear();

        }).on('clear', function(){

            assert.equal($container.children().length, 0, 'the container children are removed');

            QUnit.start();
        })
        .init()
        .render($container);
    });

    QUnit.asyncTest('No clear in the provider', function(assert){
        QUnit.expect(1);

        var $container = $('#item-container');


        itemRunner.register('dummyProvider', _.omit(dummyProvider, 'clear'));

        var runner = itemRunner('dummyProvider', {
            type: 'search'
        }).on('render', function(){

            this.clear();

        }).on('clear', function(){

            assert.ok(true, 'clear is still called');

            QUnit.start();
        })
        .init()
        .render($container);
    });


// itemRunner().get/setState()
//             .on('statechange')

    module('ItemRunner state', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('setState after render', function(assert){
        QUnit.expect(4);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            value : 0
        }).on('render', function(){
            var $input = $('input', $container);
            assert.equal($input.length, 1, 'the container contains an input');
            assert.equal($input.val(), 0, 'the input value is set before');

            this.setState({ value : 12 });

            assert.equal($input.val(), 12, 'the input value has changed regarding to the state');

            QUnit.start();
        })
        .init()
        .render($container);
    });

    QUnit.asyncTest('set initial state', function(assert){
        QUnit.expect(3);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number'
        }).on('render', function(){
            var $input = $('input', $container);
            assert.equal($input.length, 1, 'the container contains an input');
            assert.equal($input.val(), 13, 'the input value has the initial state');

            QUnit.start();
        })
        .init()
        .setState({ value : 13 })
        .render($container);
    });

    QUnit.asyncTest('set a wrong state', function(assert){
        QUnit.expect(2);

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            value : 0
        })
        .on('error', function(message){
            assert.ok(typeof message === 'string', 'An error message is given');
            assert.ok(message.length > 0 , 'A non empty message is given');
            QUnit.start();
        })
        .init()
        .setState([]);
    });

    QUnit.asyncTest('get the current state', function(assert){
        QUnit.expect(7);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            value : 0
        }).on('render', function(){
            var state;
            var $input = $('input', $container);
            assert.equal($input.length, 1, 'the container contains an input');
            assert.equal($input.val(), 0, 'the input value is set before');

            state = this.getState();

            assert.ok(typeof state === 'object' , 'the state is an object');
            assert.equal(state.value, 0, 'got the initial state');

            $input.val(14);

            state = this.getState();

            assert.ok(typeof state === 'object', 'the state is an object');
            assert.equal(state.value, 14, 'got the last state value');

            QUnit.start();
        })
        .init()
        .render($container);
    });

    QUnit.asyncTest('listen for state change', function(assert){
        QUnit.expect(5);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            value : 0
        }).on('statechange', function(state){

            var $input = $('input', $container);

            assert.ok(typeof state === 'object' , 'the state is an object');
            assert.equal($input.length, 1, 'the container contains an input');
            assert.equal(state.value, 16, 'the state has the updated value');
            assert.equal($input.val(), state.value, 'the given state match the input value');

            QUnit.start();
        }).on('render', function(){
            var $input = $('input', $container);
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("change", false, true);
            $input.val(16)[0].dispatchEvent(evt);
        })
        .init()
        .render($container);
    });


// itemRunner().getResponses

    module('ItemRunner getResponses', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('getResponses with no changes', function(assert){
        QUnit.expect(4);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            value : 0
        }).on('render', function(){

            var responses = this.getResponses();

            assert.ok(responses instanceof Array, 'responses is an array');
            assert.equal(responses.length, 1, 'responses contains one entry');
            assert.equal(responses[0], 0, 'response is the initial value');

            QUnit.start();
        })
        .init()
        .render($container);
    });

    QUnit.asyncTest('getResponses after changes', function(assert){
        QUnit.expect(4);

        var $container = $('#item-container');
        assert.equal($container.length, 1, 'the item container exists');

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            value : 0
        }).on('render', function(){

            var $input = $('input', $container);
            $input.val(18);

            var responses = this.getResponses();

            assert.ok(responses instanceof Array, 'responses is an array');
            assert.equal(responses.length, 1, 'responses contains one entry, the last response only');
            assert.equal(responses[0], 18, 'response is the initial value');

            QUnit.start();
        })
        .init()
        .render($container);
    });

// itemRunner().on().off().trigger()

    module('ItemRunner events', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('multiple events binding', function(assert){
        QUnit.expect(2);

        var inc = 0;

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number'
        }).on('test', function(){
            assert.equal(inc, 0, 'handler called first');
            inc++;
        }).on('test', function(){
            assert.equal(inc, 1, 'first called 2nd');
            QUnit.start();
        })
        .init()
        .trigger('test');
    });

    QUnit.asyncTest('unbinding events', function(assert){
        QUnit.expect(1);

        var inc = 0;

        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number'
        }).on('test', function(){
            assert.ok(false, 'Should not be called');
        }).on('test', function(){
            assert.ok(false, 'should not be callled');
        })
        .init()
        .off('test')
        .trigger('test');

        setTimeout(function(){
            assert.ok(true, 'handlers not called after off');
            QUnit.start();
        }, 10);
    });
});

