define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/containerSize'
], function(_, preProcessorFactory, containerSizeProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(containerSizeProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(containerSizeProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(containerSizeProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'multiple',
        operands : [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 3
        }
    },{
        title : 'ordered',
        operands : [{
            cardinality : 'multiple',
            baseType : 'float',
            value: [2.5, 3.8, 7]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 3
        }
    },{
        title : 'null operand',
        operands : [null],
        expectedResult : {
            cardinality : 'single',
            baseType : 'integer',
            value : 0
        }
    }];

    QUnit
      .cases(dataProvider)
      .test('containerSize ', function(data, assert){
        containerSizeProcessor.operands = data.operands;
        containerSizeProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(containerSizeProcessor.process(), data.expectedResult, 'The containerSize is correct');
    });
});
