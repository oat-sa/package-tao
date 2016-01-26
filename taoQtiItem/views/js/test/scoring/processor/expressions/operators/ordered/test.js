define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/errorHandler',
    'taoQtiItem/scoring/processor/expressions/operators/ordered'
], function(_, preProcessorFactory, errorHandler, orderedProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(orderedProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(orderedProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(orderedProcessor.operands), 'the processor has operands');
    });

    module('Process');

    QUnit.asyncTest('Fails if operands are not of the same baseType', function(assert){
        QUnit.expect(1);
        orderedProcessor.operands = [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'float',
            value: 5.5
        }];

        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Operands must be of the same type');
            QUnit.start();
        });
        orderedProcessor.preProcessor = preProcessorFactory({});
	    orderedProcessor.process();
    });


    var dataProvider = [{
        title : 'ordered integer',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7, 5]
        }
    },{
        title: 'ordered directedPair',
        operands: [{
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7]]
        }, {
            cardinality: 'single',
            baseType: 'directedPair',
            value: [5, 10]
        }],
        expectedResult: {
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7], [5, 10]]
        }
    },{
        title : 'ordered integer with nulls',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },null,{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2, 3, 7, 5, 5]
        }
    },{
        title : 'null operand',
        operands : [null,null],
        expectedResult : null
    },{
        title : 'no operands',
        operands : [],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('ordered ', function(data, assert){
        orderedProcessor.operands = data.operands;
        orderedProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(orderedProcessor.process(), data.expectedResult, 'The ordered is correct');
    });
});
