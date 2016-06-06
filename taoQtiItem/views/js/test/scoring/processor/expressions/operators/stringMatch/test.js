define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/stringMatch'
], function (_, preProcessorFactory, stringMatchProcessor) {
    'use strict';

    module('API');

    QUnit.test('structure', function (assert) {
        assert.ok(_.isPlainObject(stringMatchProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(stringMatchProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(stringMatchProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title: 'match truth caseSensitive',
        caseSensitive: true,
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'The Rain in'
        }, {
            cardinality: 'single',
            baseType: 'string',
            value: 'The Rain in'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    }, {
        title: 'match false caseSensitive',
        caseSensitive: true,
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'The Rain in'
        }, {
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
        title: 'match true caseInSensitive',
        caseSensitive: false,
        operands: [{
            cardinality: 'single',
            baseType: 'string',
            value: 'The Rain in'
        }, {
            cardinality: 'single',
            baseType: 'string',
            value: 'The rain in'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    }, {
        title: 'one null',
        caseSensitive: true,
        operands: [null, {
            cardinality: 'single',
            baseType: 'string',
            value: 'The rain in'
        }],
        expectedResult: null
    }
    ];

    QUnit
        .cases(dataProvider)
        .test('stringMatch ', function (data, assert) {
            stringMatchProcessor.operands = data.operands;
            stringMatchProcessor.expression = { attributes : { caseSensitive : data.caseSensitive } };
            stringMatchProcessor.preProcessor = preProcessorFactory({});
            assert.deepEqual(stringMatchProcessor.process(), data.expectedResult, 'The stringMatch is correct');
        });
});
