define(['core/store', 'core/promise'], function(store, Promise){
    'use strict';

    var data = {};
    var mockBackend = function(name){

        if(!name){
            throw new TypeError('no name');
        }
        return {
            getItem : function getItem(key){
                return Promise.resolve(data[key]);
            },
            setItem : function setItem(key, value){
                data[key] = value;
                return Promise.resolve(true);
            },
            removeItem : function removeItem(key){
                delete data[key];
                return Promise.resolve(true);
            },
            clear : function clear(){
                data = {};
                return Promise.resolve(true);
            }
        };
    };

    QUnit.module('API');

    QUnit.test("module", function(assert){
        QUnit.expect(3);

        assert.ok(typeof store !== 'undefined', "The module exports something");
        assert.ok(typeof store === 'function', "The module exposes a function");
        assert.ok(typeof store.backends === 'object', "The module has a backends object");
    });

    QUnit.test("factory", function(assert){
        QUnit.expect(6);

        assert.throws(function(){
            store();
        }, TypeError, 'A storeName is likely required');

        assert.throws(function(){
            store('foo', 'bar');
        }, TypeError, 'A backend is a function');

        assert.throws(function(){
            store('foo', function(){ return 'bar'; });
        }, TypeError, 'A backend is a function that returns a storage');

        assert.throws(function(){
            store('foo', function(){
                return  {
                    getItem : function(){}
                };
            });
        }, TypeError, 'A backend is a function that returns a complete storage');

        store('foo', mockBackend);

        assert.ok(typeof store('foo') === 'object', "The factory creates an object");
        assert.notEqual(store('foo'), store('foo'), "The factory creates an new object");

    });


    QUnit.module('CRUD', {
        setup    : function(){
            data = {};
        }
    });

    QUnit.asyncTest("setItem", function(assert){
        QUnit.expect(4);

        var storage = store('foo', mockBackend);
        assert.equal(typeof storage, 'object', 'The store is an object');

        var p = storage.setItem('bar', 'boz');
        assert.ok(p instanceof Promise, 'setItem returns a Promise');

        p.then(function(result){

            assert.equal(typeof result, 'boolean', 'The result is a boolean');
            assert.ok(result, 'The item is added');

            QUnit.start();
        }).catch(function(err){
            assert.ok(false, err);
            QUnit.start();
        });
    });

    QUnit.asyncTest("getItem", function(assert){
        QUnit.expect(5);

        var storage = store('foo', mockBackend);
        assert.equal(typeof storage, 'object', 'The store is an object');

        var p = storage.setItem('bar', 'noz');
        assert.ok(p instanceof Promise, 'setItem returns a Promise');

        p.then(function(result){
            assert.ok(result, 'The item is added');

            storage.getItem('bar').then(function(value){

                assert.equal(typeof value, 'string', 'The result is a string');
                assert.equal(value, 'noz', 'The retrieved value is correct');

                QUnit.start();
            });
        }).catch(function(err){
            assert.ok(false, err);
            QUnit.start();
        });
    });

    QUnit.asyncTest("removeItem", function(assert){
        QUnit.expect(5);

        var storage = store('foo', mockBackend);
        assert.equal(typeof storage, 'object', 'The store is an object');

        storage.setItem('moo', 'noob')
        .then(function(result){
            assert.ok(result, 'The item is added');

            return storage.getItem('moo').then(function(value){
                assert.equal(value, 'noob', 'The retrieved value is correct');
            });
        }).then(function(){
            return storage.removeItem('moo').then(function(rmResult){
                    assert.ok(rmResult, 'The item is removed');
                });
        }).then(function(){
            return storage.getItem('moo').then(function(value){
                assert.equal(typeof value, 'undefined', 'The value does not exists anymore');
                QUnit.start();
            });
        }).catch(function(err){
            assert.ok(false, err);
            QUnit.start();
        });
    });

    QUnit.asyncTest("clear", function(assert){
        QUnit.expect(5);

        var storage = store('foo', mockBackend);
        assert.equal(typeof storage, 'object', 'The store is an object');

        Promise.all([
            storage.setItem('zoo', 'zoob'),
            storage.setItem('too', 'toob')
        ])
        .then(function(){
            return storage.getItem('too').then(function(value){
                assert.equal(value, 'toob', 'The retrieved value is correct');
            });
        }).then(function(){
            return storage.clear().then(function(rmResult){
                    assert.ok(rmResult, 'The item is removed');
                });
        }).then(function(){
            return storage.getItem('too').then(function(value){
                assert.equal(typeof value, 'undefined', 'The value does not exists anymore');
                return storage.getItem('zoo').then(function(value){
                    assert.equal(typeof value, 'undefined', 'The value does not exists anymore');
                    QUnit.start();
                });
            });
        }).catch(function(err){
            assert.ok(false, err);
            QUnit.start();
        });
    });

    QUnit.asyncTest("object", function(assert){
        QUnit.expect(3);

        var sample = {
            collection : [{
                item1: true,
                item2: 'false',
                item3: 12
            },{
                item4: { value : null }
            }]
        };
        var storage = store('foo', mockBackend);
        assert.equal(typeof storage, 'object', 'The store is an object');

        storage.setItem('sample', sample).then(function(added){
            assert.ok(added, 'The item is added');
            storage.getItem('sample').then(function(result){
                assert.deepEqual(result, sample, 'Retrieving the sample');
                QUnit.start();
            });
        }).catch(function(err){
            assert.ok(false, err);
            QUnit.start();
        });
    });
});
