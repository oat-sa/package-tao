define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/anyN'
], function(_, preProcessorFactory, anyNProcessor){
    "use strict";

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(anyNProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(anyNProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(anyNProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'truth',
        min: 2,
        max: 4,
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'truth with ref',
        min: 'min',
        max: 'max',
        state:{
            min:{
                cardinality : 'single',
                baseType : 'integer',
                value : 2
            },
            max:{
                cardinality : 'single',
                baseType : 'integer',
                value : 4
            }
        },
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'false - too much true',
        min: 1,
        max: 2,
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'false - lack of true',
        min: 2,
        max: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'Null',
        min: 3,
        max: 4,
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        },{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }, null],
        expectedResult :null
    },{
        title : 'true with Null',
        min: 3,
        max: 4,
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        },{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, null],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'false with Null',
        min: 3,
        max: 4,
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        },{
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }, null],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    }];

    QUnit
    .cases(dataProvider)
    .test('anyN ', function (data, assert) {
        anyNProcessor.preProcessor = preProcessorFactory(data.state ? data.state : {});
        anyNProcessor.operands = data.operands;
        anyNProcessor.expression = {attributes: {min: data.min, max: data.max}};
        assert.deepEqual(anyNProcessor.process(), data.expectedResult, 'The anyN is correct');
    });
});
