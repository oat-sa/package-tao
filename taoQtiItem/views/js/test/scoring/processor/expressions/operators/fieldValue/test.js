define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/fieldValue'
], function (_, preProcessorFactory, fieldValueProcessor) {
    'use strict';

    module('API');

    QUnit.test('structure', function (assert) {
        assert.ok(_.isPlainObject(fieldValueProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(fieldValueProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(fieldValueProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
            title: 'found field',
            fieldIdentifier: 'paper',
            operands: [
                {
                    cardinality: 'record',
                    value: {
                        rock: {
                            cardinality: 'single',
                            baseType: 'integer',
                            value: 10.222
                        },
                        paper: {
                            cardinality: 'multiple',
                            baseType: 'string',
                            value: ['p', 'a', 'p', 'e', 'r']
                        },
                        scissors: {
                            cardinality: 'multiple',
                            baseType: 'integer',
                            value: [1, 2, 3, 4]
                        }
                    }
                }
            ],
            expectedResult: {
                cardinality: 'multiple',
                baseType: 'string',
                value: ['p', 'a', 'p', 'e', 'r'].sort()
            }
        },{
        title: 'missing field',
        fieldIdentifier: 'meal',
        operands: [
            {
                cardinality: 'record',
                value: {
                    rock: {
                        cardinality: 'single',
                        baseType: 'integer',
                        value: 10.222
                    },
                    paper: {
                        cardinality: 'multiple',
                        baseType: 'string',
                        value: ['p', 'a', 'p', 'e', 'r']
                    },
                    scissors: {
                        cardinality: 'multiple',
                        baseType: 'integer',
                        value: [1, 2, 3, 4]
                    }
                }
            }
        ],
        expectedResult: null
    }];

    QUnit
        .cases(dataProvider)
        .test('fieldValue ', function (data, assert) {
            fieldValueProcessor.operands = data.operands;
            fieldValueProcessor.preProcessor = preProcessorFactory({});

            fieldValueProcessor.expression = {
                attributes: {
                    fieldIdentifier: data.fieldIdentifier
                }
            };
            assert.deepEqual(fieldValueProcessor.process(), data.expectedResult, 'The fieldValue is correct');
        });
})
;
