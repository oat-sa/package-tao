define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/isNull'
], function(_, preProcessorFactory, isNullProcessor){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(isNullProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(isNullProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(isNullProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'not null integer',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '5'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'not null falsy',
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
        title : 'null value',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : null
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'null operand',
        operands : [null],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    }];

    QUnit
      .cases(dataProvider)
      .test('isNull ', function(data, assert){
        isNullProcessor.operands = data.operands;
        isNullProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(isNullProcessor.process(), data.expectedResult, 'The isNull is correct');
    });
});
