define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/truncate'
], function(_, preProcessorFactory, truncateProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(truncateProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(truncateProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(truncateProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'floats',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.123'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20
        }
    },{
        title : 'integers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '20'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20
        }
    },{
        title : 'Infinity',
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
    },{
        title : '-Infinity',
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
    },{
        title : 'NaN',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : NaN
        }],
        expectedResult : null
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, null],
        expectedResult : null
    }
    ];

    QUnit
      .cases(dataProvider)
      .test('truncate ', function(data, assert){
        truncateProcessor.operands = data.operands;
        truncateProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(truncateProcessor.process(), data.expectedResult, 'The truncate is correct');
    });
});
