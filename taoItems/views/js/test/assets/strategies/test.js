define([
    'taoItems/assets/manager',
    'taoItems/assets/strategies'
], function(assetManagerFactory, strategies){


    QUnit.module('API');

    QUnit.test('module', 2, function(assert){
        assert.ok(typeof strategies !== 'undefined', "The module exports something");
        assert.ok(typeof strategies === 'object', "The module exports an object");
    });

    QUnit.module('BaseUrl strategy');

    QUnit.test('expected strategy format', 3, function(assert){

        assert.ok(typeof strategies.baseUrl === 'object', "The baseUrl strategy exists");
        assert.equal(strategies.baseUrl.name, 'baseUrl', "The baseUrl strategy has the right name");
        assert.ok(typeof strategies.baseUrl.handle === 'function', "The baseUrl strategy has an handler");
    });

    var baseUrlDataProvider = [{
        title    : 'absolute URL',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : 'http://tao.localdomain/test/test.html',
        resolved : '',
    }, {
        title    : 'relative root URL',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : '/test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : 'test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL slash in baseUrl',
        baseUrl  : 'http://tao.localdomain/',
        slashcat   : true,
        url      : '/test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL current directory',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : './test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL current directory',
        baseUrl  : 'http://tao.localdomain?path=',
        url      : './test/test.html',
        resolved : 'http://tao.localdomain?path=test/test.html'
    }, {
        title    : 'relative URL current directory with encodable characters',
        baseUrl  : 'http://tao.localdomain?path=',
        url      : './test/t>es+t.html',
        resolved : 'http://tao.localdomain?path=test/t%3Ees%2Bt.html'
    } ,{
        title    : 'relative URL current directory with encodable characters',
        slashcat   : true,
        baseUrl  : 'http://tao.localdomain?path=',
        url      : './test/t>es+t.html',
        resolved : 'http://tao.localdomain?path=/test/t%3Ees%2Bt.html'
    }];

    QUnit
        .cases(baseUrlDataProvider)
        .test('resolve ', function(data, assert){
            var assetManager = assetManagerFactory(strategies.baseUrl, data);
            assert.equal(assetManager.resolve(data.url), data.resolved, 'The Url is resolved');
        });

    QUnit.module('External strategy');

    QUnit.test('expected strategy format', 3, function(assert){

        assert.ok(typeof strategies.external === 'object', "The external strategy exists");
        assert.equal(strategies.external.name, 'external', "The external strategy has the right name");
        assert.ok(typeof strategies.external.handle === 'function', "The external strategy has an handler");
    });

    var externalDataProvider = [{
        title    : 'absolute URL',
        url      : 'http://tao.localdomain/test/test.html',
        resolved : 'http://tao.localdomain/test/test.html',
    }, {
        title    : 'relative root URL',
        url      : '/test/test.html',
        resolved : ''
    }, {
        title    : 'FTP absolute URL',
        url      : 'ftp://tao.localdomain',
        resolved : 'ftp://tao.localdomain'
    }, {
        title    : 'relative URL current directory',
        url      : './test/test.html',
        resolved : ''
    }];

    QUnit
        .cases(externalDataProvider)
        .test('resolve ', function(data, assert){
            var assetManager = assetManagerFactory(strategies.external);
            assert.equal(assetManager.resolve(data.url), data.resolved, 'The Url is resolved');
        });

    QUnit.module('Base64 strategy');

    QUnit.test('expected strategy format', 3, function(assert){

        assert.ok(typeof strategies.base64 === 'object', "The base64 strategy exists");
        assert.equal(strategies.base64.name, 'base64', "The base64 strategy has the right name");
        assert.ok(typeof strategies.base64.handle === 'function', "The base64 strategy has an handler");
    });

    var base64DataProvider = [{
        title    : 'absolute URL',
        url      : 'http://tao.localdomain/test/test.html',
        resolved : '',
    }, {
        title    : 'relative root URL',
        url      : '/test/test.html',
        resolved : ''
    }, {
        title    : 'FTP absolute URL',
        url      : 'ftp://tao.localdomain',
        resolved : ''
    }, {
        title    : 'relative URL current directory',
        url      : './test/test.html',
        resolved : ''
    }, {
        title    : 'encoded URL',
        url      : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFiSURBVBgZpcEhbpRRGIXh99x7IU0asGBJWEIdCLaAqcFiCArFCkjA0KRJF0EF26kkFbVVdEj6/985zJ0wBjfp8ygJD6G3n358fP3m5NvtJscJYBObchEHx6QKJ6SKsnn6eLm7urr5/PP76cU4eXVy/ujouD074hDHd5s6By7GZknb3P7mUH+WNLZGKnx595JDvf96zTQSM92vRYA4lMEEO5RNraHWUDH3FV48f0K5mAYJk5pQQpqIgixaE1JDKtRDd2OsYfJaTKNcTA2IBIIesMAOPdDUGYJSqGYml5lGHHYkSGhAJBBIkAoWREAT3Z3JLqZhF3uS2EloQCQ8xLBxoAEWO7aZxros7EgISIIkwlZCY6s1OlAJTWFal5VppMzUgbAlQcIkiT0DXSI2U2ymYZs9AWJL4n+df3pncsI0bn5dX344W05dhctUFbapZcE2ToiLVHBMbGymS7aUhIdoPNBf7Jjw/gQ77u4AAAAASUVORK5CYII=',
        resolved : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFiSURBVBgZpcEhbpRRGIXh99x7IU0asGBJWEIdCLaAqcFiCArFCkjA0KRJF0EF26kkFbVVdEj6/985zJ0wBjfp8ygJD6G3n358fP3m5NvtJscJYBObchEHx6QKJ6SKsnn6eLm7urr5/PP76cU4eXVy/ujouD074hDHd5s6By7GZknb3P7mUH+WNLZGKnx595JDvf96zTQSM92vRYA4lMEEO5RNraHWUDH3FV48f0K5mAYJk5pQQpqIgixaE1JDKtRDd2OsYfJaTKNcTA2IBIIesMAOPdDUGYJSqGYml5lGHHYkSGhAJBBIkAoWREAT3Z3JLqZhF3uS2EloQCQ8xLBxoAEWO7aZxros7EgISIIkwlZCY6s1OlAJTWFal5VppMzUgbAlQcIkiT0DXSI2U2ymYZs9AWJL4n+df3pncsI0bn5dX344W05dhctUFbapZcE2ToiLVHBMbGymS7aUhIdoPNBf7Jjw/gQ77u4AAAAASUVORK5CYII='
    }];

    QUnit
        .cases(base64DataProvider)
        .test('resolve ', function(data, assert){
            var assetManager = assetManagerFactory(strategies.base64);
            assert.equal(assetManager.resolve(data.url), data.resolved, 'The Url is resolved');
        });
});

