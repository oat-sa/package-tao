define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/match'
], function(_, preProcessorFactory, matchProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(matchProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(matchProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(matchProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'integer',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '3'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'integer',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 3
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '3'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'identifier',
        operands : [{
            cardinality : 'single',
            baseType : 'identifier',
            value : 'over'
        }, {
            cardinality : 'single',
            baseType : 'identifier',
            value : 'over'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'identifier',
        operands : [{
            cardinality : 'single',
            baseType : 'identifier',
            value : 'overfalse'
        }, {
            cardinality : 'single',
            baseType : 'identifier',
            value : 'over'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'boolean',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
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
        title : 'boolean',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : 'true'
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : 'false'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'boolean',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }, {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'pair',
        operands : [{
            cardinality : 'single',
            baseType : 'pair',
            value : ['id1','id2']
        }, {
            cardinality : 'single',
            baseType : 'pair',
            value : ['id2','id1']
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'pair',
        operands : [{
            cardinality : 'single',
            baseType : 'pair',
            value : ['id1','id2']
        }, {
            cardinality : 'single',
            baseType : 'pair',
            value : ['id1','id2']
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'pair',
        operands : [{
            cardinality : 'single',
            baseType : 'pair',
            value : ['id1','id2']
        }, {
            cardinality : 'single',
            baseType : 'pair',
            value : ['id1','id3']
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'pair multiple',
        operands : [{
            cardinality : 'multiple',
            baseType : 'pair',
            value : [['id1','id2'],['id2','id3']]
        }, {
            cardinality : 'multiple',
            baseType : 'pair',
            value : [['id1','id2'],['id2','id5']]
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'directedPair',
        operands : [{
            cardinality : 'single',
            baseType : 'directedPair',
            value : ['id1','id2']
        }, {
            cardinality : 'single',
            baseType : 'directedPair',
            value : ['id1','id2']
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'directedPair',
        operands : [{
            cardinality : 'single',
            baseType : 'directedPair',
            value : ['id2','id1']
        }, {
            cardinality : 'single',
            baseType : 'directedPair',
            value : ['id1','id2']
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        },
            null, {
                cardinality : 'single',
                baseType : 'integer',
                value : 2
            }],
        expectedResult : null
    },
    ];

    QUnit
      .cases(dataProvider)
      .test('match ', function(data, assert){
        matchProcessor.operands = data.operands;
        matchProcessor.preProcessor = preProcessorFactory({});
        assert.deepEqual(matchProcessor.process(), data.expectedResult, 'The match is correct');
    });
});
