define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/integerToFloat'
], function(_, preProcessorFactory, integerToFloatProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(integerToFloatProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(integerToFloatProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(integerToFloatProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'integer',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '10'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 10
        }
    },{
        title : 'integer',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 10
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 10
        }
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        },
        null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('integerToFloat ', function(data, assert){
        integerToFloatProcessor.operands = data.operands;
        integerToFloatProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(integerToFloatProcessor.process(), data.expectedResult, 'The integerToFloat is correct');
    });
});
