define(['core/encoder/str2array'], function(str2array){
    'use strict';

    QUnit.module('str2array');


    QUnit.test('module', function (assert) {
        QUnit.expect(3);
        assert.ok(typeof str2array === 'object', 'the module exposes an object');
        assert.ok(typeof str2array.encode === 'function', 'the module exposes the encode method');
        assert.ok(typeof str2array.decode === 'function', 'the module exposes the decode method');
    });


    QUnit.module('encoder');

    var encodeData = [
        { title: 'empty array', input : [], output: ''},
        { title: 'string', input : 'foo', output: 'foo'},
        { title: 'string array', input : ['bar', 'foo'], output: 'bar,foo'},
        { title: 'colon glue', input : ['bar', 'foo'], output: 'bar;foo', glue : ';'}
    ];

    QUnit
        .cases(encodeData)
        .test('encode', 1, function (data, assert) {
            QUnit.expect(1);
            assert.deepEqual(str2array.encode(data.input, data.glue), data.output , 'Encoding ');
        });


    QUnit.module('decoder');

    var decodeData = [
        { title: 'empty string', input : '', output: []},
        { title: 'blank string', input : ' ', output: []},
        { title: 'number', input : 12, output: []},
        { title: 'null', input : null, output: []},
        { title: 'string', input : 'foo', output: ['foo']},
        { title: 'string with comma', input : 'bar,foo', output: ['bar','foo']},
        { title: 'colon glue', input : 'bar;foo', output: ['bar','foo'], glue : ';'},
    ];

    QUnit
        .cases(decodeData)
        .test('decode', 1, function (data, assert) {
            QUnit.expect(1);
            assert.deepEqual(str2array.decode(data.input, data.glue), data.output , 'Decoding ');
        });
});


