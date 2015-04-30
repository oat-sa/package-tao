define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/errorHandler',
    'taoQtiItem/scoring/processor/expressions/operators/delete'
], function(_, preProcessorFactory, errorHandler, deleteProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(deleteProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(deleteProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(deleteProcessor.operands), 'the processor has operands');
    });

    QUnit.module('Process', {
        teardown: function() {
            errorHandler.reset('scoring');
        }
    });

      QUnit.asyncTest('Fails if the 1st operand is not single', function(assert){
        QUnit.expect(1);
        deleteProcessor.operands = [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3]
        },{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [7, 2, 3, 2]
        }];

        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'The first operand must have a single cardinality');
            QUnit.start();
        });
        deleteProcessor.preProcessor = preProcessorFactory({});
	    deleteProcessor.process();
    });

    QUnit.asyncTest('Fails if operands are not of the same type', function(assert){
        QUnit.expect(1);
        deleteProcessor.operands = [{
            cardinality : 'single',
            baseType : 'pairs',
            value: [2, 3]
        },{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [7, 2, 3, 2]
        }];

        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Operands must have the same baseType');
            QUnit.start();
        });
        deleteProcessor.preProcessor = preProcessorFactory({});
	    deleteProcessor.process();
    });

    var dataProvider = [{
        title : 'multiple',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value: 2
        },{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [7, 2, 3, 2]
        }],
        expectedResult : {
            cardinality : 'multiple',
            baseType : 'integer',
            value: [7, 3].sort()
        }
    },{
        title : 'ordered',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value: 2
        },{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [7, 2, 3, 2]
        }],
        expectedResult : {
            cardinality : 'ordered',
            baseType : 'integer',
            value: [7, 3]
        }
    },{
        title : 'incorrect baseType',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value: 2
        },{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [7, 2, 3, 2]
        }],
        expectedResult : null
    },{
        title : 'incorrect cardinality',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value: 2
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: [7, 2, 3, 2]
        }],
        expectedResult : null
    },{
        title : 'null operand',
        operands : [null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('delete ', function(data, assert){
        deleteProcessor.operands = data.operands;
        deleteProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(deleteProcessor.process(), data.expectedResult, 'The delete is correct');
    });
});
