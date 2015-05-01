define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/roundTo'
], function(_, preProcessorFactory, roundToProcessor){
    'use strict';

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(roundToProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(roundToProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(roundToProcessor.operands), 'the processor has a process function');
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
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20.115
        }
    },{
        title : 'figures as negative',
        roundingMode: 'significantFigures',
        figures: -10,
        operands : [{
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
        }],
        expectedResult : null
    },{
        title : 'figures as string ',
        roundingMode: 'significantFigures',
        figures: '3',
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20.115
        }
    },{
        title : 'figures as incorrect string ',
        roundingMode: 'significantFigures',
        figures: 'xxx',
        operands : [{
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
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20.115
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
        }],
        expectedResult : null
    },{
        title : 'decimalPlaces',
        roundingMode: 'decimalPlaces',
        figures: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20.114
        }
    },{
        title : 'decimalPlaces with 0 figures',
        roundingMode: 'decimalPlaces',
        figures: 0,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '20.1145'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 20
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
    },{
        title : '+Inf',
        roundingMode: 'significantFigures',
        figures: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : Infinity
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : Infinity
        }
    },{
        title : '-Inf',
        roundingMode: 'significantFigures',
        figures: 3,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : -Infinity
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : -Infinity
        }
    }
    ];

    QUnit
      .cases(dataProvider)
      .test('roundTo ', function(data, assert){
        roundToProcessor.operands = data.operands;

        roundToProcessor.state = data.state ? data.state : {};
        roundToProcessor.preProcessor = preProcessorFactory(data.state ? data.state : {});

        roundToProcessor.expression = {
            attributes: {
                figures: data.figures,
                roundingMode: data.roundingMode
            }
        };

        assert.deepEqual(roundToProcessor.process(), data.expectedResult, 'The roundTo is correct');
    });
});
