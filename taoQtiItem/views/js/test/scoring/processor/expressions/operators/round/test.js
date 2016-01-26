define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/round'
], function(_, preProcessorFactory, roundProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(roundProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(roundProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(roundProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'float up',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.5145'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 21
        }
    },{
        title : 'float down',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20
        }
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, null],
        expectedResult : null
    },{
        title : '+Inf',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : Infinity
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : Infinity
        }
    }, {
        title: 'Nan',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }],
        expectedResult: null
    },{
        title : '-Inf',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : -Infinity
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : -Infinity
        }
    }
    ];

    QUnit
      .cases(dataProvider)
      .test('round ', function(data, assert){
        roundProcessor.operands = data.operands;
        roundProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(roundProcessor.process(), data.expectedResult, 'The round is correct');
    });
});
