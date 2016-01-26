define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/divide'
], function(_, preProcessorFactory, divideProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(divideProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(divideProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(divideProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'integers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '6'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 3
        }
    },{
        title : 'integer and float',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '6.5'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 3.25
        }
    },{
        title : 'zero test',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 5.5
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 0
        }
    },{
        title : 'zero test 2',
        operands : [ {
            cardinality : 'single',
            baseType : 'float',
            value : 0.666677
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : 0
        }],
        expectedResult : null
    },{
        title : 'overflow',
        operands : [ {
            cardinality : 'single',
            baseType : 'float',
            value : Number.MIN_VALUE
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : 10
        }],
        expectedResult : null
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
      .test('divide ', function(data, assert){
        divideProcessor.operands = data.operands;
        divideProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(divideProcessor.process(), data.expectedResult, 'The divide is correct');
    });
});
