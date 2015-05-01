define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/member'
], function(_, preProcessorFactory, memberProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(memberProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(memberProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(memberProcessor.operands), 'the processor has operands');
    });

    module('Process');

    var dataProvider = [{
        title : 'multiple truth',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value: 7
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
        title : 'string false',
        operands : [{
            cardinality : 'single',
            baseType : 'string',
            value: 'xx,'
        },{
            cardinality : 'multiple',
            baseType : 'string',
            value: [ 'xx', 'yy' ]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'different basetypes',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value: 7
        },{
            cardinality : 'ordered',
            baseType : 'string',
            value: [ 5, 7 ]
        }],
        expectedResult : null
    },{
        title : 'multiple false',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value: 22
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
    }, {
        title: 'incorrect cardinality',
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
            cardinality: 'single',
            baseType: 'directedPair',
            value: [2, 3]
        }, {
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[5, 8], [2, 3], [4, 7]]
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'ordered false directedPair',
        operands: [{
            cardinality: 'single',
            baseType: 'directedPair',
            value: [2, 3]
        }, {
            cardinality: 'ordered',
            baseType: 'directedPair',
            value: [[3, 2], [4, 22]]
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title : 'null operand',
        operands : [null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('member ', function(data, assert){
        memberProcessor.operands = data.operands;
        memberProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(memberProcessor.process(), data.expectedResult, 'The member is correct');
    });
});
