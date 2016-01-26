define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/not'
], function(_, preProcessorFactory, notProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(notProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(notProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(notProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'boolean string',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : 'true'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'boolean',
        operands : [{
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
        title : 'boolean number',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : '1'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
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
      .test('not ', function(data, assert){
        notProcessor.operands = data.operands;
        notProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(notProcessor.process(), data.expectedResult, 'The not is correct');
    });
});
