define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/lcm'
], function(_, preProcessorFactory, lcmProcessor){
    "use strict";

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(lcmProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(lcmProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(lcmProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'integers',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '16'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '32'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '8'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 32
        }
    },{
        title : 'multiply integers',
        operands : [{
            cardinality : 'multiply',
            baseType : 'integer',
            value: [10, 20, 30]
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '900'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '70'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 6300
        }
    },{
        title : 'all zeros',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '0'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }
    },{
        title : 'not all zeros',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '0'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }
    },{
        title : 'Is null',
        operands : [null,{
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        }],
        expectedResult : null
    },{
        title : 'null on wrong values with multiple cardinality operand',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, {
            cardinality : 'multiple',
            baseType : 'integer',
            value : [5, 7, Infinity]
        }],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('lcm ', function(data, assert){
        lcmProcessor.operands = data.operands;
        lcmProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(lcmProcessor.process(), data.expectedResult, 'The lcm is correct');
    });
});
