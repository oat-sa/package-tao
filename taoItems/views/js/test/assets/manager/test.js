define([
    'taoItems/assets/manager'
], function(assetManagerFactory){


    QUnit.module('API');

    QUnit.test('module', 7, function(assert){
        assert.ok(typeof assetManagerFactory !== 'undefined', "The module exports something");
        assert.ok(typeof assetManagerFactory === 'function', "The module exports a function");

        var assetManager = assetManagerFactory();

        assert.ok(typeof assetManager === 'object', "The factory creates an object");
        assert.ok(typeof assetManager.addStrategy === 'function', "The manager has a method addStrategy");
        assert.ok(typeof assetManager.resolve === 'function', "The manager has a method resolve");
        assert.ok(typeof assetManager.resolveBy === 'function', "The manager has a method resolveBy");
        assert.ok(typeof assetManager.clearCache === 'function', "The manager has a method clearCache");
    });

    QUnit.module('Strategy');

    QUnit.test('expected strategy format', 5, function(assert){

        var assetManager = assetManagerFactory();

        assert.throws(function(){
            assetManager.addStrategy(null);
        }, TypeError, 'The strategy must be an object');

        assert.throws(function(){
            assetManager.addStrategy({
                foo : true
            });
        }, TypeError, 'The strategy must have a name');

        assert.throws(function(){
            assetManager.addStrategy({
                name : 'foo'
            });
        }, TypeError, 'The strategy must have a handle method');

        assert.throws(function(){
            assetManager.addStrategy({
               name : null
            });
        }, TypeError, 'The strategy must have a name');

        var strategy = {
            name : 'foo',
            handle : function(){}
        };

        assetManager.addStrategy(strategy);

        assert.equal(assetManager._strategies[0].name, strategy.name, 'The strategy has been added');
    });


    QUnit.test('strategy resolution', 3, function(assert){

        var strategy = {
            name : 'foo',
            handle : function(path, data){
                return 'foo' + path ;
            }
        };

        var assetManager = assetManagerFactory(strategy);
        assert.equal(assetManager._strategies.length, 1, 'There is one strategy');
        assert.equal(assetManager._strategies[0].name, strategy.name, 'The strategy has been added');

        var result = assetManager.resolve('bar');
        assert.equal(result, 'foobar', 'The strategy has resolved');
    });

    QUnit.test('multiple strategies resolution', 8, function(assert){

        var assetManager = assetManagerFactory([{
            name : 'foo',
            handle : function(path, data){
                if(path.toString() === 'far'){
                    return 'foo' + path ;
                }
            }
        }, {
            name : 'boo',
            handle : function(path, data){
                if(path.toString() === 'bar'){
                    return 'boo' + path ;
                }
            }
        }]);

        assert.equal(assetManager._strategies.length, 2, 'There are 2 strategies');
        assert.equal(assetManager._strategies[0].name, 'foo', 'The foo strategy has been added');
        assert.equal(assetManager._strategies[1].name, 'boo', 'The boo strategy has been added');

        var res1 = assetManager.resolve('far');
        assert.equal(res1, 'foofar', 'The path is resolved by foo');

        var res2 = assetManager.resolve('bar');
        assert.equal(res2, 'boobar', 'The path is resolved by boo');

        var res3 = assetManager.resolveBy('foo', 'far');
        assert.equal(res3, 'foofar', 'The path is resolved by foo');

        var res4 = assetManager.resolve('moo');
        assert.equal(res4, '', 'The path is not resolved');

        var res5 = assetManager.resolveBy('too');
        assert.equal(res5, '', 'The path is not resolved');
    });

    QUnit.test('anonymous strategies', 4, function(assert){

        var assetManager = assetManagerFactory([
        function(path, data){
            if(path.toString() === 'far'){
                return 'foo' + path ;
            }
        }, function(path, data){
            if(path.toString() === 'bar'){
                return 'boo' + path ;
            }
        }]);


        assert.equal(assetManager._strategies.length, 2, 'There are 2 strategies');

        var res1 = assetManager.resolve('far');
        assert.equal(res1, 'foofar', 'The path is resolved by foo');

        var res2 = assetManager.resolve('bar');
        assert.equal(res2, 'boobar', 'The path is resolved by boo');

        var res3 = assetManager.resolve('moo');
        assert.equal(res3, '', 'The path is not resolved');

    });

    QUnit.module('Options');

    QUnit.test('create a data context', 10, function(assert){

        var base = "http://t.ao/";
        var otherBase = "https://tao.test/";
        var path = 'bar.html';

        var strategies = [{
            name : 'foo',
            handle : function(path, data){
                return  data.base + path ;
            }
        }];

        var assetManager = assetManagerFactory(strategies, {base : base });

        var otherAssetManager = assetManagerFactory(strategies, {base : otherBase});

        assert.notEqual(assetManager, otherAssetManager, "The 2 asset manager are differents");

        assert.equal(assetManager._strategies.length, 1, 'There is one strategy');
        assert.equal(assetManager._strategies[0].name, 'foo', 'The strategy has been added');

        assert.equal(otherAssetManager._strategies.length, 1, 'There is one strategy');
        assert.equal(otherAssetManager._strategies[0].name, 'foo', 'The strategy has been added');


        var res1 = assetManager.resolve(path);
        assert.equal(res1, base + path , 'The path is resolved');
        assert.equal(res1, 'http://t.ao/bar.html', 'The path is resolved');

        var res2 = otherAssetManager.resolve(path);
        assert.equal(res2, otherBase + path , 'The path is resolved');
        assert.equal(res2, 'https://tao.test/bar.html', 'The path is resolved');

        assert.notEqual(res1, res2, 'The resolution is different in contexts');
    });

    QUnit.test('update the data context', 10, function(assert){

        var base = "http://t.ao/";
        var base2 = "https://tao.test/";
        var base3 = "//taotesting.com/";
        var path = 'bar.html';

        var strategies = [{
            name : 'foo',
            handle : function(path, data){
                return  data.base + path ;
            }
        }];

        var assetManager = assetManagerFactory(strategies, {base : base });

        assert.equal(assetManager.getData('base'), base, 'The base are the same');
        assert.deepEqual(assetManager.getData(), { base : base }, 'The context is the same');

        var res1 = assetManager.resolve(path);
        assert.equal(res1, base + path , 'The path is resolved');
        assert.equal(res1, 'http://t.ao/bar.html', 'The path is resolved');

        assetManager.setData('base', base2);
        assert.equal(assetManager.getData('base'), base2, 'The base are the same');

        var res2 = assetManager.resolve(path);
        assert.equal(res2, base2 + path , 'The path is resolved');
        assert.equal(res2, 'https://tao.test/bar.html', 'The path is resolved');

        assetManager.setData( { 'base' :  base3});
        assert.equal(assetManager.getData('base'), base3, 'The base are the same');

        var res3 = assetManager.resolve(path);
        assert.equal(res3, base3 + path , 'The path is resolved');
        assert.equal(res3, '//taotesting.com/bar.html', 'The path is resolved');
    });
    QUnit.test('use caching', 8, function(assert){

        var strategy = {
            name : 'foo',
            handle : function(url, data){
                data.counter++;
                return 'match_' + data.counter;
            }
        };

        var noCacheAssetManager = assetManagerFactory(strategy, {counter : 0}, { cache : false });

        assert.equal(noCacheAssetManager.resolve('bar.html'), 'match_1', 'The url resolve from strategy');
        assert.equal(noCacheAssetManager.resolve('bar.html'), 'match_2', 'The url resolve from strategy');
        assert.equal(noCacheAssetManager.resolve('bar.html'), 'match_3', 'The url resolve from strategy');

        var cacheAssetManager = assetManagerFactory(strategy, {counter : 0}, { cache : true });

        assert.equal(cacheAssetManager.resolve('bar.html'), 'match_1', 'The url resolve from strategy');
        assert.equal(cacheAssetManager.resolve('bar.html'), 'match_1', 'The url resolve from cache');
        assert.equal(cacheAssetManager.resolve('bar.html'), 'match_1', 'The url resolve from cache');

        cacheAssetManager.clearCache();
        assert.equal(cacheAssetManager.resolve('bar.html'), 'match_2', 'The url resolve from strategy after clearing the cache');
        assert.equal(cacheAssetManager.resolve('bar.html'), 'match_2', 'The url resolve from cache');
    });


    QUnit.test('url parsing', 2, function(assert){

        var strategy = {
            name : 'port',
            handle : function(url, data){
                return url.protocol + '://' + url.host + ':8080' + url.path;
            }
        };

        var assetManager = assetManagerFactory(strategy, {}, { parseUrl : true });

        var res1 = assetManager.resolve('http://taotesting.com/tao/download.html');
        assert.equal(res1,  'http://taotesting.com:8080/tao/download.html', 'The path is resolved');

        var res2 = assetManager.resolve('https://taotesting.com/tao/download.html?foo=bar');
        assert.equal(res2,  'https://taotesting.com:8080/tao/download.html', 'The path is resolved');
    });
});

