define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/or'
], function(_, preProcessorFactory, orProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(orProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(orProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(orProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'truth strings',
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
            value : true
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
            value : false
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
        title : 'truth  with 3 operands',
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
            value : true
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'false with 3 operands',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : false
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
      .test('or ', function(data, assert){
        orProcessor.operands = data.operands;
        orProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(orProcessor.process(), data.expectedResult, 'The or is correct');
    });
});
