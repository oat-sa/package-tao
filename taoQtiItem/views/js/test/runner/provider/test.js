define([
    'jquery',
    'lodash',
    'taoItems/runner/api/itemRunner',
    'taoQtiItem/runner/provider/qti',
    'json!taoQtiItem/test/samples/json/space-shuttle.json',
    'json!taoQtiItem/test/samples/json/space-shuttle-pic.json'
], function($, _, itemRunner, qtiRuntimeProvider, itemData, itemDataPic){
    'use strict';

    var runner;
    var containerId = 'item-container';


    QUnit.module('Provider API');

    QUnit.test('module', function(assert){
        assert.ok(typeof qtiRuntimeProvider !== 'undefined', "The module exports something");
        assert.ok(typeof qtiRuntimeProvider === 'object', "The module exports an object");
        assert.ok(typeof qtiRuntimeProvider.init === 'function' || typeof qtiRuntimeProvider.render === 'function', "The provider expose an init or a render method");
    });



    QUnit.module('Register the provider', {
        teardown : function(){
            //reset the provider
            itemRunner.providers = undefined;
        }
    });

    QUnit.test('register the qti provider', function(assert){
        QUnit.expect(4);

        assert.ok(typeof itemRunner.providers === 'undefined', 'the runner has no providers');

        itemRunner.register('qti', qtiRuntimeProvider);

        assert.ok(typeof itemRunner.providers === 'object', 'the runner has now providers');
        assert.ok(typeof itemRunner.providers.qti === 'object', 'the runner has now the qti providers');
        assert.equal(itemRunner.providers.qti, qtiRuntimeProvider, 'the runner has now the qti providers');

    });



    QUnit.module('Provider init', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Item data loading', function(assert){
        QUnit.expect(2);

        itemRunner.register('qti', qtiRuntimeProvider);


        itemRunner('qti', itemData)
          .on('init', function(){

            assert.ok(typeof this._item === 'object', 'The item data is loaded and mapped to an object');
            assert.ok(typeof this._item.bdy === 'object', 'The item contains a body object');

            QUnit.start();
          }).init();
    });

    QUnit.asyncTest('Loading wrong data', function(assert){
        QUnit.expect(2);

        itemRunner.register('qti', qtiRuntimeProvider);

        itemRunner('qti', { foo : true})
          .on('error', function(message){

            assert.ok(true, 'The provider triggers an error event');
            assert.ok(typeof message === 'string', 'The error is a string');

            QUnit.start();
          }).init();
    });


    QUnit.module('Provider render', {
        teardown : function(){
            //reset the provides
            runner.clear();
            itemRunner.providers = undefined;

        }
    });

    QUnit.asyncTest('Item rendering', function(assert){
        QUnit.expect(3);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');
        assert.equal(container.children.length, 0, 'the container has no children');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
            .on('render', function(){

                assert.equal(container.children.length, 1, 'the container has children');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('Issue in rendering', function(assert){
        QUnit.expect(4);
        var count = 0;
        var container = document.getElementById(containerId);

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
            .on('init', function(){
                this._item.renderer = null;
                this.render(container);
            })
            .on('error', function(message){
                assert.ok(true, 'The provider triggers an error event');
                assert.ok(typeof message === 'string', 'The error is a string');
                if(count > 0){
                   QUnit.start();
                }
                count++;
            })
            .init();
    });



    QUnit.module('Provider clear', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Clear a rendered item', function(assert){
        QUnit.expect(4);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');
        assert.equal(container.children.length, 0, 'the container has no children');

        itemRunner.register('qti', qtiRuntimeProvider);

        itemRunner('qti', itemData)
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



    QUnit.module('Provider state', {
        teardown : function(){
            //reset the provides
            runner.clear();
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('default state structure', function(assert){
        QUnit.expect(4);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
            .on('render', function(){
                var state  = this.getState();

                assert.ok(typeof state === 'object' , 'the state is an object');
                assert.ok(typeof state.RESPONSE === 'object' , 'the state contains the interaction response identifier');
                assert.ok(typeof state.RESPONSE.response === 'object' , 'the state contains the interaction response');

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('get state after changes', function(assert){
        QUnit.expect(12);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
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

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
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

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
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

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
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

    QUnit.module('Provider responses', {
        teardown : function(){
            //reset the provides
            runner.clear();
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('no responses set', function(assert){
        QUnit.expect(4);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
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

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemData)
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


    QUnit.module('Provider PIC', {
        teardown : function(){
            //reset the provides
            runner.clear();
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Get the list of PIC', function(assert){
        QUnit.expect(17);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemDataPic)
            .on('listpic', function(picManager){
                assert.ok(typeof picManager === 'object', 'the pic manager is an object');
                assert.ok(typeof picManager.getList === 'function', 'the pic manager has a getList method');

                var picList = picManager.getList();

                assert.ok(picList instanceof Array, 'the list is an array');
                assert.equal(picList.length, 4, 'the list contains 4 items');
                _.each(picList, function(pic, i) {
                    assert.ok(typeof pic === 'object' , 'element ' + i + ' is an object');
                    assert.ok(typeof pic.getPic === 'function', 'the pic manager has a getPic method');
                    assert.equal(pic.getPic().qtiClass, 'infoControl', 'element ' + i + ' is a PIC manager');
                });

                QUnit.start();
            })
            .init()
            .render(container);
    });

    QUnit.asyncTest('Get a particular PIC', function(assert){
        QUnit.expect(17);

        var container = document.getElementById(containerId);

        assert.ok(container instanceof HTMLElement , 'the item container exists');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemDataPic)
            .on('listpic', function(picManager){
                assert.ok(typeof picManager === 'object', 'the pic manager is an object');
                assert.ok(typeof picManager.getPic === 'function', 'the pic manager has a getPic method');

                var pic;

                pic = picManager.getPic('picMock1');
                assert.ok(typeof pic === 'object' , 'the pic manager is an object');
                assert.ok(typeof pic.getPic === 'function', 'the pic manager has a getPic method');
                assert.ok(typeof pic.getSerial === 'function', 'the pic manager has a getSerial method');
                assert.ok(typeof pic.getTypeIdentifier === 'function', 'the pic manager has a getTypeIdentifier method');
                assert.equal(pic.getPic().qtiClass, 'infoControl', 'it\'s a PIC manager');
                assert.equal(pic.getTypeIdentifier(), 'picMock1', 'the PIC has the right identifier');
                assert.equal(pic.getSerial(), 'portableinfocontrol_556f08e21039e128137685', 'the PIC has the right serial');

                pic = picManager.getPic('portableinfocontrol_556f08e211b49955612514');
                assert.ok(typeof pic.getPic === 'function', 'the pic manager has a getPic method');
                assert.ok(typeof pic.getSerial === 'function', 'the pic manager has a getSerial method');
                assert.ok(typeof pic.getTypeIdentifier === 'function', 'the pic manager has a getTypeIdentifier method');
                assert.equal(pic.getPic().qtiClass, 'infoControl', 'it\'s a PIC manager');
                assert.equal(pic.getTypeIdentifier(), 'picMock2', 'the PIC has the right identifier');
                assert.equal(pic.getSerial(), 'portableinfocontrol_556f08e211b49955612514', 'the PIC has the right serial');

                pic = picManager.getPic('portableinfocontrol_1234567890123456789012');
                assert.ok(typeof pic === 'undefined' , 'the unknown pic is undefined');

                QUnit.start();
            })
            .init()
            .render(container);
    });


    QUnit.asyncTest('PIC state', function(assert){
        QUnit.expect(7);

        var container = document.getElementById(containerId);
        assert.ok(container instanceof HTMLElement , 'the item container exists');

        itemRunner.register('qti', qtiRuntimeProvider);

        runner = itemRunner('qti', itemDataPic)
            .on('render', function(){
                var state = this.getState();
                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.pic === 'object', 'The state has a pic object');
                assert.ok(typeof state.pic['mock-1'] === 'object', 'The state of the mock-1 pic is an object');
                assert.ok(typeof state.pic['mock-2'] === 'object', 'The state of the mock-2 pic is an object');
                assert.ok(typeof state.pic['mock-3'] === 'object', 'The state of the mock-3 pic is an object');

                state.pic['mock-2'].foo = 'bar';

                this.setState(state);

                var newState = this.getState();
                assert.equal(newState.pic['mock-2'].foo, 'bar', 'The state values is set and retrieved');
                QUnit.start();
            })
            .init()
            .render(container);
    });
});

