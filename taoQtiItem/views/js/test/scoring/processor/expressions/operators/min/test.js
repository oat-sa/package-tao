define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/min'
], function(_, preProcessorFactory, minProcessor){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(minProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(minProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(minProcessor.operands), 'the processor has a process function');
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
            value : 2
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
            value : 2
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
            value : 5.333323
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
            value : 2
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
            value : 3
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
        .test('min ', function(data, assert){
            minProcessor.operands = data.operands;
            minProcessor.preProcessor = preProcessorFactory({});
            assert.deepEqual(minProcessor.process(), data.expectedResult, 'The min is correct');
        });
});
