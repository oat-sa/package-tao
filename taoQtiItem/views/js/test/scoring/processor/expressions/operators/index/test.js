define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/errorHandler',
    'taoQtiItem/scoring/processor/expressions/operators/index'
], function(_, preProcessorFactory, errorHandler, indexProcessor){
    'use strict';

    QUnit.module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(indexProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(indexProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(indexProcessor.operands), 'the processor has operands');
    });

    QUnit.module('Process', {
        teardown: function() {
            errorHandler.reset('scoring');
        }
    });

    QUnit.asyncTest('Fails if n, the index is 0', function(assert){
        QUnit.expect(1);
        indexProcessor.operands = [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 6, 9, 10]
        }];

        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'The index is one based');
            QUnit.start();
        });

        indexProcessor.expression = { attributes : { n : 0 } };
        indexProcessor.preProcessor = preProcessorFactory({});
	    indexProcessor.process();
    });

    var dataProvider = [{
        title : 'exists',
        n: 3,
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 6, 9, 10]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value: 9
        }
    },{
        title : 'exists - n is reference',
        n: 'ref1',
        state: {
            ref1: {
                cardinality: 'single',
                baseType: 'integer',
                value: '3'
            }
        },
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 6, 9, 10]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value: 9
        }
    },{
        title : 'incorrect n',
        n: -1,
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 6, 9, 10]
        }],
        expectedResult : null
    },{
        title : 'n - out of the range ',
        n: 10,
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 6, 9, 10]
        }],
        expectedResult : null
    },{
        title : 'null operand',
        n: 2,
        operands : [null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('index ', function(data, assert){
        indexProcessor.operands = data.operands;
        indexProcessor.state = data.state || {};
        indexProcessor.preProcessor = preProcessorFactory(data.state || {});
        indexProcessor.expression = { attributes : { n : data.n } };
        assert.deepEqual(indexProcessor.process(), data.expectedResult, 'The index is correct');
    });
});
