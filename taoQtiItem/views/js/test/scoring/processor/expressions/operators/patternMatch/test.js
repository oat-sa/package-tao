define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/patternMatch'
], function (_, preProcessorFactory, patternMatchProcessor) {
    'use strict';

    module('API');

    QUnit.test('structure', function (assert) {
        assert.ok(_.isPlainObject(patternMatchProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(patternMatchProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(patternMatchProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title: 'don\'t match',
        pattern: 'rain',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'The rain in'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title: ' match with dot',
        pattern: 'ra(.*)in',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: "raalksjaslkdjin"
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'don\'t match',
        pattern: '^rain$',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'rain'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title: 'match - escaping for ^',
        pattern: 'ra^in',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'ra^in'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: ' match ',
        pattern: '.*rain.*',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'the rain was'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: ' match ',
        pattern: '\\d{1,2}',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 99
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: ' don\'t match ',
        pattern: '.*rain.*',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'the Rain was'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title: ' match ',
        pattern: '.*ra/in.*',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'the ra/in was'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'match',
        pattern: 'rain',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'rain'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'match - ref',
        pattern: 'ref1',
        state: {
            ref1: {
                cardinality: 'single',
                baseType: 'string',
                value: 'rain'
            }
        },
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'rain'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'don\'t - match - ref(exists)',
        pattern: 'rain',
        state: {
            rain: {
                cardinality: 'single',
                baseType: 'string',
                value: 'hidden'
            }
        },
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'rain'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title: 'don\'t - match - ref(missing)',
        pattern: 'rain',
        state: {
            draw: {
                cardinality: 'single',
                baseType: 'string',
                value: 'hidden'
            }
        },
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'rain'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'don\'t match',
        pattern: 'car',
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'The rain in'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    }, {
        title: 'one null',
        pattern: /car/,
        operands: [null],
        expectedResult: null
    }
    ];

    QUnit
        .cases(dataProvider)
        .test('patternMatch ', function (data, assert) {
            patternMatchProcessor.preProcessor = preProcessorFactory(data.state ? data.state : {});
            patternMatchProcessor.operands = data.operands;
            patternMatchProcessor.expression = { attributes : { pattern : data.pattern } };
            assert.deepEqual(patternMatchProcessor.process(), data.expectedResult, 'The patternMatch is correct');
        });
});
