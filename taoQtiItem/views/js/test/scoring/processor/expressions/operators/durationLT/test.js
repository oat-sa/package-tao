define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/durationLT'
], function(_, preProcessorFactory, durationLTProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(durationLTProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(durationLTProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(durationLTProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'less',
        operands : [{
            cardinality : 'single',
            baseType : 'duration',
            value : '100000'
        }, {
            cardinality : 'single',
            baseType : 'duration',
            value : '100001'
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
            value : false
        }
    },{
        title : 'less',
        operands : [{
            cardinality : 'single',
            baseType : 'duration',
            value : '100000.1'
        }, {
            cardinality : 'single',
            baseType : 'duration',
            value : '100000'
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
      .test('durationLT ', function(data, assert){
        durationLTProcessor.operands = data.operands;
        durationLTProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(durationLTProcessor.process(), data.expectedResult, 'The durationLT is correct');
    });
});
