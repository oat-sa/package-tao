define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/errorHandler',
    'taoQtiItem/scoring/processor/expressions/operators/statsOperator'
], function(_, preProcessorFactory, errorHandler, statsProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(statsProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(statsProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(statsProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'mean',
        name: 'mean',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value: 4
        }
    },{
        title : 'incorrect stat operation',
        name: 'magic',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        }],
        expectedResult : null
    },{
        title : 'sampleVariance',
        name: 'sampleVariance',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [600, 470, 170, 430, 300]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value: 27130
        }
    },{
        title : 'sampleVariance',
        name: 'sampleVariance',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [20]
        }],
        expectedResult : null
    },{
        title : 'sampleSD',
        name: 'sampleSD',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [20]
        }],
        expectedResult : null
    },{
        title : 'sampleSD',
        name: 'sampleSD',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [600, 470, 170, 430, 300]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value: 164.7118696390761
        }
    },{
        title : 'popVariance',
        name: 'popVariance',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [600, 470, 170, 430, 300]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value: 21704
        }
    },{
        title : 'popSD',
        name: 'popSD',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [600, 470, 170, 430, 300]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value: 147.32277488562318
        }
    },{
        title : 'null operand',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, NaN, 7]
        }],
        expectedResult : null
    },{
        title : 'null operand',
        operands : [null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('ordered ', function(data, assert){
        statsProcessor.operands = data.operands;
        statsProcessor.expression = {
            attributes: {
                name: data.name
            }
        };
        statsProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(statsProcessor.process(), data.expectedResult, 'The statsOperator is correct');
    });
});
