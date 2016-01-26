define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/repeat'
], function(_, preProcessorFactory, repeatProcessor){
    'use strict';
    
    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(repeatProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(repeatProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(repeatProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'repeat integer',
        numberRepeats: 2,
        operands : [{
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7, 5, 2, 3, 7, 5]
        }
    },{
        title : 'repeat-1 integer',
        numberRepeats: 1,
        operands : [{
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7, 5]
        }
    },{
        title : 'repeat integer with ref',
        numberRepeats: 'ref1',
        state: {
            ref1: {
                cardinality: 'single',
                baseType: 'integer',
                value: '2'
            }
        },
        operands : [{
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : {
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7, 5, 2, 3, 7, 5]
        }
    },{
        title : 'repeat integer with nulls',
        numberRepeats: 3,
        operands : [{
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2]
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
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 5, 5, 2, 5, 5, 2, 5, 5]
        }
    },{
        title: 'ordered directedPair',
        numberRepeats: 2,
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
            value: [[2, 3], [4, 7], [5, 10],[2, 3], [4, 7], [5, 10]]
        }
    }, {
        title : 'different baseTypes',
        numberRepeats: 3,
        operands : [{
            cardinality: 'ordered',
            baseType : 'float',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: [5]
        }],
        expectedResult : null
    },{
        title : 'null operand',
        numberRepeats: 3,
        operands : [null,null],
        expectedResult : null
    },{
        title : 'no operands',
        numberRepeats: 3,
        operands : [],
        expectedResult : null
    },{
        title : 'incorrect numberRepeats',
        numberRepeats: -1,
        operands : [{
            cardinality: 'ordered',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'single',
            baseType : 'integer',
            value: 5
        }],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('repeat ', function(data, assert){
        repeatProcessor.operands = data.operands;
        repeatProcessor.expression = { attributes : { numberRepeats : data.numberRepeats } };
        repeatProcessor.state = data.state ? data.state : {};
        repeatProcessor.preProcessor = preProcessorFactory(data.state ? data.state : {});
        assert.deepEqual(repeatProcessor.process(), data.expectedResult, 'The repeat is correct');
    });
});
