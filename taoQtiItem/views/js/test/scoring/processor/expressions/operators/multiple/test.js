define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/multiple'
], function(_, preProcessorFactory, multipleProcessor){
    'use strict';
    
    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(multipleProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(multipleProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(multipleProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'multiple integer',
        operands : [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7, 5]
        }
    },{
        title: 'multiple directedPair',
        operands: [{
            cardinality: 'multiple',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7]]
        }, {
            cardinality: 'single',
            baseType: 'directedPair',
            value: [5, 10]
        }],
        expectedResult: {
            cardinality: 'multiple',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7], [5, 10]]
        }
    },{
        title : 'multiple integer with nulls',
        operands : [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7]
        },null,{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7, 5, 5]
        }
    },{
        title : 'different baeTypes',
        operands : [{
            cardinality : 'multiple',
            baseType : 'float',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: [5]
        }],
        expectedResult : null
    },{
        title : 'null operand',
        operands : [null,null],
        expectedResult : null
    },{
        title : 'no operands',
        operands : [],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('multiple  ', function(data, assert){
        multipleProcessor.preProcessor = preProcessorFactory({});
        multipleProcessor.operands = data.operands;
        assert.deepEqual(multipleProcessor.process(), data.expectedResult, 'The multiple is correct');
    });
});
