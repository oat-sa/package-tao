define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/contains'
], function(_, preProcessorFactory, containsProcessor){
    'use strict';
    
    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(containsProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(containsProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(containsProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'multiple truth',
        operands : [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [ 7, 3 ]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'multiple false',
        operands : [{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [2, 3, 7]
        },{
            cardinality : 'multiple',
            baseType : 'integer',
            value: [ 4, 2 ]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'ordered truth',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2 , 5, 7]
        },{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [ 5, 7]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'different basetypes',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2 , 5, 7]
        },{
            cardinality : 'ordered',
            baseType : 'string',
            value: [ 5, 7 ]
        }],
        expectedResult : null
    }, {
        title: 'different cardinality',
        operands: [{
            cardinality: 'ordered',
            baseType: 'integer',
            value: [2, 5, 7]
        }, {
            cardinality: 'multiple',
            baseType: 'integer',
            value: [5, 7]
        }],
        expectedResult: null
    },{
        title: 'ordered truth directedPair',
        operands: [{
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3], [7, 10], [2, 3], [4, 7]]
        }, {
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7]]
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'multiply truth directedPair',
        operands: [{
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7]]
        }, {
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3]]
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'ordered false directedPair',
        operands: [{
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[2, 3], [4, 7]]
        }, {
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[3, 2 ]]
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title : 'ordered false',
        operands : [{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [2 , 5, 7]
        },{
            cardinality : 'ordered',
            baseType : 'integer',
            value: [ 7 , 5]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'null operand',
        operands : [null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('contains ', function(data, assert){
        containsProcessor.operands = data.operands;
        containsProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(containsProcessor.process(), data.expectedResult, 'The contains is correct');
    });
});
