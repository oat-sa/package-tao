define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/integerDivide'
], function(_, preProcessorFactory, integerModulusProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(integerModulusProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(integerModulusProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(integerModulusProcessor.operands), 'the processor has a process function');
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
            baseType : 'integer',
            value : 2
        }
    },{
        title : 'zero test',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }
    },{
        title : 'zero test 2',
        operands : [ {
            cardinality : 'single',
            baseType : 'integer',
            value : 0.666677
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }],
        expectedResult : null
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
      .test('integerModulus ', function(data, assert){
        integerModulusProcessor.operands = data.operands;
        integerModulusProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(integerModulusProcessor.process(), data.expectedResult, 'The integerModulus is correct');
    });
});
