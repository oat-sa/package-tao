define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/gte'
], function(_, preProcessorFactory, gteProcessor){
    "use strict";

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(gteProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(gteProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(gteProcessor.operands), 'the processor has a process function');
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
            value : true
        }
    },{
        title : 'integers equality',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '5'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '5'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
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
            value : true
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
            value : false
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
            value :false
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
      .test('gte ', function(data, assert){
        gteProcessor.operands = data.operands;
        gteProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(gteProcessor.process(), data.expectedResult, 'The gte is correct');
    });
});
