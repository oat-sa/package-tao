define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/space-shuttle.json'
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
                assert.equal(state.RESPONSE.response.base, null, 'the default state contains a null base');

                //change something
                $('[data-identifier="Discovery"]', $(container)).click();
                //debugger;

                state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response.base === 'object' , 'the contains a base object');
                assert.equal(state.RESPONSE.response.base.identifier, 'Discovery', 'the contains the selected choice');

                //change something else
                $('[data-identifier="Atlantis"]', $(container)).click();

                state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response.base === 'object' , 'the contains a base object');
                assert.equal(state.RESPONSE.response.base.identifier, 'Atlantis', 'the contains the selected choice');

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

                assert.ok( ! $('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is not checked');

                this.setState({ RESPONSE : { response : { base : { identifier : 'Atlantis' } } } });

                assert.ok($('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is checked');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('set multiple  states', function(assert){
        QUnit.expect(8);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('render', function(){

                assert.ok( ! $('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is not checked');

                this.setState({ RESPONSE : { response : { base : { identifier : 'Atlantis' } } } });

                assert.ok($('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is checked');

                 //change something
                $('[data-identifier="Discovery"]', $(container)).click();

                assert.ok( ! $('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is not checked');
                assert.ok($('[data-identifier="Discovery"] input', $(container)).prop('checked'), 'The choice is checked');

                this.setState({ RESPONSE : { response : { base : { identifier : 'Challenger' } } } });

                assert.ok( ! $('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is not checked');
                assert.ok( ! $('[data-identifier="Discovery"] input', $(container)).prop('checked'), 'The choice is not checked');
                assert.ok($('[data-identifier="Challenger"] input', $(container)).prop('checked'), 'The choice is checked');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('listen state changes', function(assert){
        QUnit.expect(10);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        runner = qtiItemRunner('qti', itemData)
            .on('statechange', function(state){

                assert.ok($('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is checked');

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response.base === 'object' , 'the contains a base object');
                assert.equal(state.RESPONSE.response.base.identifier, 'Atlantis', 'the contains the selected choice');

                QUnit.start();
            })
            .on('render', function(){
                var state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.equal(state.RESPONSE.response.base, null, 'the default state contains a null base');

                assert.ok( ! $('[data-identifier="Atlantis"] input', $(container)).prop('checked'), 'The choice is not checked');

                $('[data-identifier="Atlantis"]', $(container)).click();
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
                assert.equal(responses.RESPONSE.base, null, 'the response contains a null base property');

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
                assert.equal(responses.RESPONSE.base, null, 'the response contains a null base property');

                //the user set response
                $('[data-identifier="Atlantis"]', $(container)).click();

                responses = this.getResponses();

                assert.ok(typeof responses === 'object' , 'the response is an object');
                assert.ok(typeof responses.RESPONSE === 'object' , 'the response contains the interaction response identifier');
                assert.equal(responses.RESPONSE.base.identifier, 'Atlantis', 'the response contains the set value');

                QUnit.start();
            })
            .init()
            .render(container);
    });
});

