define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/power'
], function(_, preProcessorFactory, powerProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(powerProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(powerProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(powerProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'integers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 4
        }
    },{
        title : 'integers from numbers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 3
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2.5
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 9
        }
    },{
        title : 'floats',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : "2.1"
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : "3.2"
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 10.74241047739471
        }
    },{
        title : 'one float',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 4
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : 0.5
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 2
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
        title : 'Overflow max range',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : Number.MAX_VALUE
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : null
    },{
        title : 'Overflow min range',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : Number.MIN_VALUE
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : null
    }
    ];

    QUnit
      .cases(dataProvider)
      .test('power ', function(data, assert){
        powerProcessor.operands = data.operands;
        powerProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(powerProcessor.process(), data.expectedResult, 'The power is correct');
    });
});
