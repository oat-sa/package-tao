define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/subtract'
], function(_, preProcessorFactory, subtractProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(subtractProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(subtractProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(subtractProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'integers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '5'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 3
        }
    },{
        title : 'integers from numbers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 15
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 5.5
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 10
        }
    },{
        title : 'floats',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : 1.333323
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : 0.666677
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 0.6666460000000001
        }
    },{
        title : 'one float',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '10.25'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : -5.25
        }
    },{
        title : 'ignore wrong values',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : Infinity
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 5
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
      .test('subtract ', function(data, assert){
        subtractProcessor.operands = data.operands;
        subtractProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(subtractProcessor.process(), data.expectedResult, 'The subtract is correct');
    });
});
