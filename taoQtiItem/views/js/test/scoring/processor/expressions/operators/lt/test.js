define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/lt'
], function(_, preProcessorFactory, ltProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(ltProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(ltProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(ltProcessor.operands), 'the processor has a process function');
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
            baseType : 'boolean',
            value : false
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
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'floats',
        operands : [ {
            cardinality : 'single',
            baseType : 'float',
            value : 0.666677
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : 1.333323
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
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
            baseType : 'boolean',
            value :true
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
      .test('lt ', function(data, assert){
        ltProcessor.operands = data.operands;
        ltProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(ltProcessor.process(), data.expectedResult, 'The lt is correct');
    });
});
