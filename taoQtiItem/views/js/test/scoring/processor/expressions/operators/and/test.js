define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/and'
], function(_, preProcessorFactory, andProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(andProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(andProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(andProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'false',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : 'true'
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
        title : 'false',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : false
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
        title : 'truth',
        operands : [{
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
            value : true
        }
    },{
        title : 'truth with single',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'false with single',
        operands : [{
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
        title : 'truth with 3 operand',
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
            value : true
        }
    },{
        title : 'false with 3 operand',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : false
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
      .test('and ', function(data, assert){
        andProcessor.operands = data.operands;
        andProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(andProcessor.process(), data.expectedResult, 'The and is correct');
    });
});
