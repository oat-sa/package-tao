define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/product'
], function(_, preProcessorFactory, productProcessor){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(productProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(productProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(productProcessor.operands), 'the processor has a process function');
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
            value : 50
        }
    },{
        title : 'integers from numbers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 2
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
            value : 20
        }
    },{
        title : 'floats',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : "0.13"
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : "0.16"
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 0.020800000000000003
        }
    },{
        title : 'one float',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 8
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : 0.25
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 4
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
            value : 2
        }, {
            cardinality : 'multiple',
            baseType : 'integer',
            value : [2, 2, 2]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 16
        }
    }];

    QUnit
      .cases(dataProvider)
      .test('product ', function(data, assert){
        productProcessor.operands = data.operands;
        productProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(productProcessor.process(), data.expectedResult, 'The product is correct');
    });
});
