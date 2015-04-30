define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/mathOperator'
], function (_, preProcessorFactory, mathOperatorProcessor) {
    'use strict';

    module('API');

    QUnit.test('structure', function (assert) {
        assert.ok(_.isPlainObject(mathOperatorProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(mathOperatorProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(mathOperatorProcessor.operands), 'the processor has a process function');
    });


    module('Process');

    var dataProvider = [{
        title: 'sin',
        name: 'sin',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 2
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'cos',
        name: 'cos',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'tan',
        name: 'tan',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'tan',
        name: 'tan',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 4
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'sec1',
        name: 'sec',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -1
        }
    }, {
        title: 'sec',
        name: 'sec',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }],
        expectedResult: null
    }, {
        title: 'sec',
        name: 'sec',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 2
        }],
        expectedResult: null
    }, {
        title: 'sec',
        name: 'sec',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 3
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 2
        }
    }, {
        title: 'asin0',
        name: 'asin',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'asin',
        name: 'asin',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 2
        }
    }, {
        title: 'asin overflow',
        name: 'asin',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1.4
        }],
        expectedResult: null
    }, {
        title: 'acos overflow',
        name: 'acos',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1.4
        }],
        expectedResult: null
    }, {
        title: 'acos',
        name: 'acos',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'asec < 1',
        name: 'asec',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0.2
        }],
        expectedResult: null
    }, {
        title: 'asec',
        name: 'asec',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -5
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1.7721542475852274
        }
    }, {
        title: 'acsc < 1',
        name: 'acsc',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0.2
        }],
        expectedResult: null
    }, {
        title: 'acsc ',
        name: 'acsc',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -5
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -0.2013579207903308
        }
    }, {
        title: 'acot ',
        name: 'acot',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -5
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -0.19739555984988078
        }
    }, {
        title: 'acot ',
        name: 'acot',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0.1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1.4711276743037345
        }
    }, {
        title: 'atan',
        name: 'atan',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 4
        }
    }, {
        title: 'atan',
        name: 'atan',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: +0
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: 10
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -0
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: 10
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 10
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -10
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 10
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 20
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 2
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -20
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Math.PI / 2
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: 20
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Math.PI / 2
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI / 4
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 3 * Math.PI / 4
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Math.PI / 4
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -3 * Math.PI / 4
        }
    }, {
        title: 'atan2',
        name: 'atan2',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }, {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: null
    }, {
        title: 'sinh',
        name: 'sinh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'sinh',
        name: 'sinh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1.1752011936438014
        }
    }, {
        title: 'cosh',
        name: 'cosh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1.5430806348152437
        }
    }, {
        title: 'cosh',
        name: 'cosh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'cosh',
        name: 'cosh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'tanh',
        name: 'tanh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'tanh',
        name: 'tanh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -1
        }
    }, {
        title: 'tanh',
        name: 'tanh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'tanh',
        name: 'tanh',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0.7615941559557649
        }
    }, {
        title: 'sech',
        name: 'sech',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0.6480542736638855
        }
    }, {
        title: 'csch',
        name: 'csch',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0.8509181282393216
        }
    }, {
        title: 'coth',
        name: 'coth',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1.3130352854993312
        }
    }, {
        title: 'log',
        name: 'log',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'log',
        name: 'log',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }
    }, {
        title: 'log',
        name: 'log',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 100
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 2
        }
    }, {
        title: 'ln',
        name: 'ln',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.E
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'log',
        name: 'log',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -1
        }],
        expectedResult: null
    }, {
        title: 'exp',
        name: 'exp',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }],
        expectedResult: null
    }, {
        title: 'exp',
        name: 'exp',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'exp',
        name: 'exp',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'exp',
        name: 'exp',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.E
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 15.154262241479264
        }
    }, {
        title: 'abs',
        name: 'abs',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Math.E
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.E
        }
    }, {
        title: 'abs',
        name: 'abs',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.E
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.E
        }
    }, {
        title: 'abs',
        name: 'abs',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'abs',
        name: 'abs',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }],
        expectedResult: null
    }, {
        title: 'signum',
        name: 'signum',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }],
        expectedResult: null
    }, {
        title: 'signum',
        name: 'signum',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 0
        }
    }, {
        title: 'signum',
        name: 'signum',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 100
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 1
        }
    }, {
        title: 'signum',
        name: 'signum',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -100
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -1
        }
    }, {
        title: 'floor',
        name: 'floor',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 100.5
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 100
        }
    }, {
        title: 'floor',
        name: 'floor',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'floor',
        name: 'floor',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }
    }, {
        title: 'ceil',
        name: 'ceil',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 100.5
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 101
        }
    }, {
        title: 'ceil',
        name: 'ceil',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'ceil',
        name: 'ceil',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }
    }, {
        title: 'toDegrees',
        name: 'toDegrees',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: 180
        }
    }, {
        title: 'toDegrees',
        name: 'toDegrees',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: NaN
        }],
        expectedResult: null
    }, {
        title: 'toDegrees',
        name: 'toDegrees',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'toDegrees',
        name: 'toDegrees',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }
    }, {
        title: 'toRadians',
        name: 'toRadians',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Infinity
        }
    }, {
        title: 'toRadians',
        name: 'toRadians',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: 180
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: Math.PI
        }
    }, {
        title: 'toRadians',
        name: 'toRadians',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'float',
            value: -Infinity
        }
    }, {
        title: 'wrong function',
        name: 'jump',
        operands: [{
            cardinality: 'single',
            baseType: 'float',
            value: '20.1145'
        }],
        expectedResult: null
    }, {
        title: 'one null',
        roundingMode: 'significantFigures',
        figures: 3,
        operands: [{
            cardinality: 'single',
            baseType: 'integer',
            value: 5
        }, null],
        expectedResult: null
    }
    ];


    var accuracy = mathOperatorProcessor.accuracy;
    QUnit
        .cases(dataProvider)
        .test('mathOperator ', function (data, assert) {
            mathOperatorProcessor.operands = data.operands;

            mathOperatorProcessor.preProcessor = preProcessorFactory(data.state ? data.state : {});

            mathOperatorProcessor.expression = {
                attributes: {
                    name: data.name
                }
            };

            var results = mathOperatorProcessor.process();
            if (_.isNull(results) || _.isNull(data.expectedResult)) {
                assert.equal(data.expectedResult, results, 'The mathOperator  is correct');
            } else {
                assert.equal(data.expectedResult.cardinality, results.cardinality, 'The mathOperator - cardinality is correct');
                assert.equal(data.expectedResult.baseType, results.baseType, ' The mathOperator is correct');
                assert.close(results.value, data.expectedResult.value, accuracy, 'The mathOperator value is correct with accuracy ' + accuracy);
            }

        });
});
