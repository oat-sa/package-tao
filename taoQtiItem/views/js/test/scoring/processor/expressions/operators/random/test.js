define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/random'
], function(_, preProcessorFactory, randomProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(randomProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(randomProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(randomProcessor.operands), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'string',
        operands : [{
            cardinality : 'multiple',
            baseType : 'string',
            value: ['xx', 'yy', 'zz']
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'string'
        }
    },{
        title : 'integer',
        operands : [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: ['2', '3', 5]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer'
        }
    },{
        title : 'pair',
        operands : [{
            cardinality : 'multiple',
            baseType : 'pair',
            value: [[2, 3], [5, 7], [10, 20]]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'pair'
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
      .test('random ', function(data, assert){
        randomProcessor.operands = data.operands;
        randomProcessor.preProcessor = preProcessorFactory({});

        var result = randomProcessor.process();
        if (_.isNull(result)) {
            assert.equal(result, data.expectedResult, 'The null check');
        } else {
            assert.equal(result.baseType, data.expectedResult.baseType, 'The random baseType is correct');
            assert.equal(result.cardinality, data.expectedResult.cardinality, 'The random cardinality is correct');
            assert.notEqual(data.operands[0].value.indexOf(result.value), -1, 'The random value is correct');
        }
    });
});
