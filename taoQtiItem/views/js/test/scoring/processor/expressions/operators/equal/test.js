define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/errorHandler',
    'taoQtiItem/scoring/processor/expressions/operators/equal'
], function(_, preProcessorFactory, errorHandler, equalProcessor){
    "use strict";

    QUnit.module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(equalProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(equalProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(equalProcessor.operands), 'the processor has a process function');
    });


  QUnit.module('Process', {
        teardown: function() {
            errorHandler.reset('scoring');
        }
    });

    QUnit.asyncTest('Fails if no tolerance is given when mode is relative', function(assert){
        QUnit.expect(1);

        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Tolerance attribute is not defined');
            QUnit.start();
        });

        equalProcessor.expression = {
            attributes : {
                includeLowerBound : false,
                includeUpperBound : false,
                toleranceMode     : 'relative'
            }
        };
        equalProcessor.operands = [{
            cardinality : 'single',
            baseType : 'float',
            value : '1'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '1.1'
        }];
        equalProcessor.preProcessor = preProcessorFactory({});
	    equalProcessor.process();
    });


    var dataProvider = [{
        title : 'integers exact',
        tolerance: '',
        toleranceMode: 'exact',
        includeLowerBound: 'true',
        includeUpperBound: 'true',

        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : '5'
        }, {
            cardinality : 'single',
            baseType : 'integer',
            value : '2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'float absolute 2 bounds',
        tolerance: '0.2 0.8',
        toleranceMode: 'absolute',
        includeLowerBound: true,
        includeUpperBound: true,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '5.2'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '5.7'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'float absolute 1 bound',
        tolerance: '0.2',
        toleranceMode: 'absolute',
        includeLowerBound: true,
        includeUpperBound: true,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '5.2'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '5.7'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'float absolute 2 bound, not include upper',
        tolerance: '0.1 0.5',
        toleranceMode: 'absolute',
        includeLowerBound: true,
        includeUpperBound: false,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '5.3'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '5.8'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'float absolute 2 bound, not include lower',
        tolerance: '0.1 0.5',
        toleranceMode: 'absolute',
        includeLowerBound: false,
        includeUpperBound: true,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '5.3'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '5.2'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'float relative 2 bound, not include lower',
        tolerance: '50 10',
        toleranceMode: 'relative',
        includeLowerBound: false,
        includeUpperBound: true,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '10'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '15'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'float relative 2 bound, not include lower, with ref',
        tolerance: 'lowBound upBound',
        state:{
            lowBound:{
                cardinality : 'single',
                baseType : 'float',
                value : 50
            },
            upBound:{
                cardinality : 'single',
                baseType : 'float',
                value : 10
            }
        },
        toleranceMode: 'relative',
        includeLowerBound: false,
        includeUpperBound: true,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '10'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '15'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : false
        }
    },{
        title : 'float relative 1 bound, not include both',
        tolerance: '50',
        toleranceMode: 'relative',
        includeLowerBound: false,
        includeUpperBound: false,
        operands : [{
            cardinality : 'single',
            baseType : 'float',
            value : '1'
        }, {
            cardinality : 'single',
            baseType : 'float',
            value : '1.1'
        }],
        expectedResult : {
            cardinality : 'single',
            baseType : 'boolean',
            value : true
        }
    },{
        title : 'one null',
        operands : [{
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        },
        null],
        expectedResult : null
    }];

    QUnit
      .cases(dataProvider)
      .test('equal ', function(data, assert){
            equalProcessor.preProcessor = preProcessorFactory(data.state ? data.state : {});

            equalProcessor.operands = data.operands;

            equalProcessor.expression = {
                attributes: {
                    includeLowerBound: data.includeLowerBound,
                    includeUpperBound: data.includeUpperBound,
                    toleranceMode: data.toleranceMode,
                    tolerance: data.tolerance
                }
            };

        assert.deepEqual(equalProcessor.process(), data.expectedResult, 'The equal is correct');
    });
});
