define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/sum'
], function(_, preProcessorFactory, sumProcessor){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(sumProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(sumProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(sumProcessor.operands), 'the processor has a process function');
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
            value : 12
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
            value : 12
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
            value : 11.0
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
            value : 17.25
        }
    },{
        title : 'ignore wrong values',
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
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 5
        }
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        },
        null, {
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
            value : 26
        }
    }];

    QUnit
      .cases(dataProvider)
      .test('sum ', function(data, assert){
        sumProcessor.operands = data.operands;
        sumProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(sumProcessor.process(), data.expectedResult, 'The sum is correct');
    });
});
