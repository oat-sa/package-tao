/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */

/**
 * Test the indexdb store backend
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['core/store/indexdb', 'core/promise'], function(indexDbBackend, Promise){
    'use strict';

    QUnit.module('API');

    QUnit.test("module", function(assert){
        QUnit.expect(2);

        assert.ok(typeof indexDbBackend !== 'undefined', "The module exports something");
        assert.ok(typeof indexDbBackend === 'function', "The module exposes a function");
    });

    QUnit.test("factory", function(assert){
        QUnit.expect(4);

        assert.throws(function(){
            indexDbBackend();
        }, TypeError, 'The backend should be created with a store id');

        assert.throws(function(){
            indexDbBackend(false);
        }, TypeError, 'The backend should be created with a valid store id');

        var store = indexDbBackend('foo');

        assert.equal(typeof store, 'object', 'The factory return an object');
        assert.notDeepEqual(indexDbBackend('foo'), store, 'The factory creates a new object');
    });

    QUnit.test("store", function(assert){
        QUnit.expect(5);
        var store = indexDbBackend('foo');

        assert.equal(typeof store, 'object', 'The store is an object');
        assert.equal(typeof store.getItem, 'function', 'The store exposes the getItem method');
        assert.equal(typeof store.setItem, 'function', 'The store exposes the setItem method');
        assert.equal(typeof store.removeItem, 'function', 'The store exposes the removetItem method');
        assert.equal(typeof store.clear, 'function', 'The store exposes the clear method');

    });


    QUnit.module('CRUD');

    QUnit.asyncTest("setItem", function(assert){
        QUnit.expect(4);

        var store = indexDbBackend('foo');
        assert.equal(typeof store, 'object', 'The store is an object');

        var p = store.setItem('bar', 'boz');
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

        var store = indexDbBackend('foo');
        assert.equal(typeof store, 'object', 'The store is an object');

        var p = store.setItem('bar', 'noz');
        assert.ok(p instanceof Promise, 'setItem returns a Promise');

        p.then(function(result){
            assert.ok(result, 'The item is added');

            store.getItem('bar').then(function(value){

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

        var store = indexDbBackend('foo');
        assert.equal(typeof store, 'object', 'The store is an object');

        store.setItem('moo', 'noob')
        .then(function(result){
            assert.ok(result, 'The item is added');

            return store.getItem('moo').then(function(value){
                assert.equal(value, 'noob', 'The retrieved value is correct');
            });
        }).then(function(){
            return store.removeItem('moo').then(function(rmResult){
                    assert.ok(rmResult, 'The item is removed');
                });
        }).then(function(){
            return store.getItem('moo').then(function(value){
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

        var store = indexDbBackend('foo');
        assert.equal(typeof store, 'object', 'The store is an object');

        Promise.all([
            store.setItem('zoo', 'zoob'),
            store.setItem('too', 'toob')
        ])
        .then(function(){
            return store.getItem('too').then(function(value){
                assert.equal(value, 'toob', 'The retrieved value is correct');
            });
        }).then(function(){
            return store.clear().then(function(rmResult){
                    assert.ok(rmResult, 'The item is removed');
                });
        }).then(function(){
            return store.getItem('too').then(function(value){
                assert.equal(typeof value, 'undefined', 'The value does not exists anymore');
                return store.getItem('zoo').then(function(value){
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
        var store = indexDbBackend('foo');
        assert.equal(typeof store, 'object', 'The store is an object');

        store.setItem('sample', sample).then(function(added){
            assert.ok(added, 'The item is added');
            store.getItem('sample').then(function(result){
                assert.deepEqual(result, sample, 'Retrieving the sample');
                QUnit.start();
            });
        }).catch(function(err){
            assert.ok(false, err);
            QUnit.start();
        });
    });


});



