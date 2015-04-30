define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/max'
], function(_, preProcessorFactory, maxProcessor){
    "use strict";

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(maxProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(maxProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(maxProcessor.operands), 'the processor has a process function');
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
            value : '5'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }
    },{
        title : 'integers from numbers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 5.5
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }
    },{
        title : 'floats',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : "5.333323"
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : "5.666677"
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 5.666677
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
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 10.25
        }
    },{
        title : 'null on wrong values',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : NaN
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : Infinity
        }],
        expectedResult : null
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        },
        null,
        {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : null
    },{
        title : 'multiple cardinality operand',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 3
        }, {
            cardinality : 'multiple',
            baseType : 'integer',
            value : [5, 7, 11]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 11
        }
    },{
        title : 'null on wrong values with multiple cardinality operand',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, {
            cardinality : 'multiple',
            baseType : 'integer',
            value : [5, 7, 11]
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : undefined
        }],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('max ', function(data, assert){
        maxProcessor.operands = data.operands;
        maxProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(maxProcessor.process(), data.expectedResult, 'The max is correct');
    });
});
