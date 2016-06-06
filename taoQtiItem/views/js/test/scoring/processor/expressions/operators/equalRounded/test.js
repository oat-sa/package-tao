define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/equalRounded'
], function(_, preProcessorFactory, equalRoundedProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(equalRoundedProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(equalRoundedProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(equalRoundedProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title : 'figures as integers',
        roundingMode: 'significantFigures',
        figures: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1147'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'figures as negative',
        roundingMode: 'significantFigures',
        figures: -10,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : null
    },{
        title : 'figures as zero with significantFigures',
        roundingMode: 'significantFigures',
        figures: 0,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : null
    },{
        title : 'figures as incorrect string ',
        roundingMode: 'significantFigures',
        figures: 'xxx',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : null
    },{
        title : 'figure as reference',
        roundingMode: 'significantFigures',
        figures: 'ref1',
        state: {
            ref1: {
                cardinality: 'single',
                baseType: 'integer',
                value: '3'
            }
        },
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1143'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'figure as missing reference',
        roundingMode: 'significantFigures',
        figures: 'ref1',
        state: {
            ref2: {
                cardinality: 'single',
                baseType: 'integer',
                value: '3'
            }
        },
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult: null
    },{
        title : 'incorrect settings',
        roundingMode: 'significantFigures',
        figures: 0,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : 12.111
        }],
        expectedResult : null
    },{
        title : 'incorrect settings',
        roundingMode: 'significantFigures',
        figures: 'string',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : 12.111
        },{
            cardinality : 'single',
            baseType : 'float',
            value : 12.111
        }],
        expectedResult : null
    },{
        title : 'decimalPlaces',
        roundingMode: 'decimalPlaces',
        figures: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1144'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1135'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'decimalPlaces with 0 figures',
        roundingMode: 'decimalPlaces',
        figures: 0,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        },{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'one null',
        roundingMode: 'significantFigures',
        figures: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        }, null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('equalRounded ', function(data, assert){
        var state = data.state || {};
        equalRoundedProcessor.operands = data.operands;

        equalRoundedProcessor.state = state;
        equalRoundedProcessor.preProcessor = preProcessorFactory(state);

        equalRoundedProcessor.expression = {
            attributes: {
                figures: data.figures,
                roundingMode: data.roundingMode
            }
        };

        assert.deepEqual(equalRoundedProcessor.process(), data.expectedResult, 'The equalRounded is correct');
    });
});
