define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/richardIII-2.json'
], function($, _, qtiItemRunner, itemData){
    'use strict';

    var runner;
    var containerId = 'item-container';

    module('Init', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('Item data loading', function(assert){
        QUnit.expect(2);

        runner = qtiItemRunner('qti', itemData)
          .on('init', function(){

            assert.ok(typeof this._item === 'object', 'The item data is loaded and mapped to an object');
            assert.ok(typeof this._item.bdy === 'object', 'The item contains a body object');

            QUnit.start();
          }).init();
    });

    module('Render', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('Item rendering', function(assert){
        QUnit.expect(3);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');
        assert.equal(container.children.length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){

                assert.equal(container.children.length, 1, 'the container has children');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    module('Clear');

    QUnit.asyncTest('Clear Item', function(assert){
        QUnit.expect(4);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');
        assert.equal(container.children.length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){
                assert.equal(container.children.length, 1, 'the container has children');

                this.clear();

            }).on('clear', function(){

                assert.equal(container.children.length, 0, 'the container children are removed');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    module('State', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('get state after changes', function(assert){
        QUnit.expect(12);

        $('#item-container').remove();
        $('#qunit-fixture').append('<div id="item-container"></div>');
        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('error', function(e){
                console.error(e);
            })
            .on('render', function(){

                //default state
                var state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.equal(state.RESPONSE.response.base.string, '', 'the default state contains an empty string');

                //change something
                $('input.qti-textEntryInteraction', $(container)).val('foo').trigger('change');

                state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response.base === 'object' , 'the contains a base object');
                assert.equal(state.RESPONSE.response.base.string, 'foo', 'the contains the entered string');

                //change something else
                $('input.qti-textEntryInteraction', $(container)).val('bar').trigger('change');

                state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response.base === 'object' , 'the contains a base object');
                assert.equal(state.RESPONSE.response.base.string, 'bar', 'the contains the entered string');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('set state', function(assert){
        QUnit.expect(3);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){

                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), '', 'The current value is empty');

                this.setState({ RESPONSE : { response : { base : { string : 'beebop' } } } });

                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), 'beebop', 'The current value matches the given state');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('set multiple  states', function(assert){
        QUnit.expect(5);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){

                //default state
                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), '', 'The current value is empty');

                //set state
                this.setState({ RESPONSE : { response :  { base : { string : 'bidiboop' } } } });
                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), 'bidiboop', 'The current value matches the given state');

                 //change something
                $('input.qti-textEntryInteraction', $(container)).val('babar').trigger('change');
                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), 'babar', 'The current value matches the given state');

                //change a new time the state
                this.setState({ RESPONSE : { response : { base : { string : 'badabeloowap' } } } });
                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), 'badabeloowap', 'The current value matches the given state');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('listen state changes', function(assert){
        QUnit.expect(9);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('statechange', function(state){

                assert.equal($('input.qti-textEntryInteraction', $(container)).val(), 'woopsy', 'The current value matches the given state');

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response.base === 'object' , 'the contains a base object');
                assert.equal(state.RESPONSE.response.base.string, 'woopsy', 'the contains the entered string');

                QUnit.start();
            })
            .on('render', function(){
                var state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.equal(state.RESPONSE.response.base.string, '', 'the default state contains an empty string');

                $('input.qti-textEntryInteraction', $(container)).val('woopsy').trigger('keyup');

            })
            .init()
            .render(container);
    });

    module('Get responses', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('no responses set', function(assert){
        QUnit.expect(4);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){
                var responses  = this.getResponses();

                assert.ok(typeof responses === 'object' , 'the response is an object');
                assert.ok(typeof responses.RESPONSE === 'object' , 'the response contains the interaction response identifier');
                assert.equal(responses.RESPONSE.base.string, '', 'the response contains an empty string');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('get responses after changes', function(assert){
        QUnit.expect(7);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){
                var responses  = this.getResponses();

                assert.ok(typeof responses === 'object' , 'the response is an object');
                assert.ok(typeof responses.RESPONSE === 'object' , 'the response contains the interaction response identifier');
                assert.equal(responses.RESPONSE.base.string, '', 'the response contains an empty string');

                //the user set response
                $('input.qti-textEntryInteraction', $(container)).val('kisscool').trigger('change');

                responses = this.getResponses();

                assert.ok(typeof responses === 'object' , 'the response is an object');
                assert.ok(typeof responses.RESPONSE === 'object' , 'the response contains the interaction response identifier');
                assert.equal(responses.RESPONSE.base.string, 'kisscool', 'the default state contains an empty string');

                QUnit.start();
            })
            .init()
            .render(container);
    });

});

