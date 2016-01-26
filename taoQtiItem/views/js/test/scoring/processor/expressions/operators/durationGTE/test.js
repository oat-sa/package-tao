define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/durationGTE'
], function(_, preProcessorFactory, durationGTEProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(durationGTEProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(durationGTEProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(durationGTEProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'greater',
        operands : [{
            cardinality : 'single',
            baseType : 'duration',
            value : '100001'
        }, {
            cardinality : 'single',
            baseType : 'duration',
            value : '100000'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'equal',
        operands : [{
            cardinality : 'single',
            baseType : 'duration',
            value : '100000'
        }, {
            cardinality : 'single',
            baseType : 'duration',
            value : '100000'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'greater',
        operands : [{
            cardinality : 'single',
            baseType : 'duration',
            value : '100000'
        }, {
            cardinality : 'single',
            baseType : 'duration',
            value : '100000.1'
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
      .test('durationGTE ', function(data, assert){
        durationGTEProcessor.operands = data.operands;
        durationGTEProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(durationGTEProcessor.process(), data.expectedResult, 'The durationGTE is correct');
    });
});
