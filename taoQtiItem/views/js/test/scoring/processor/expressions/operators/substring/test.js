define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/substring'
], function(_, preProcessorFactory, substringProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(substringProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(substringProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(substringProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'numbers',
        caseSensitive: true,
        operands : [{
            cardinality : 'single',
            baseType : 'string',
            value : 7788
        }, {
            cardinality : 'single',
            baseType : 'string',
            value : 78
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'contain substring case insensitive',
        caseSensitive: false,
        operands : [{
            cardinality : 'single',
            baseType : 'string',
            value : 'ovERwhelming'
        }, {
            cardinality : 'single',
            baseType : 'string',
            value : 'over'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'contain substring',
        caseSensitive: true,
        operands : [{
            cardinality : 'single',
            baseType : 'string',
            value : 'ovERwhelming'
        }, {
            cardinality : 'single',
            baseType : 'string',
            value : 'over'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'don\'t contain',
        caseSensitive: true,
        operands : [{
            cardinality : 'single',
            baseType : 'string',
            value : 'overwhelming'
        }, {
            cardinality : 'single',
            baseType : 'string',
            value : 'sticker'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'one null',
        caseSensitive: true,
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
      .test('substring ', function(data, assert){
        substringProcessor.operands = data.operands;
        substringProcessor.expression = { attributes : { caseSensitive : data.caseSensitive } };
        substringProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(substringProcessor.process(), data.expectedResult, 'The substring is correct');
    });
});
