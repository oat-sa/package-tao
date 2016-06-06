define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/randomInteger',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, preProcessorFactory, randomIntegerProcessor, errorHandler){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(randomIntegerProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(randomIntegerProcessor.process), 'the processor has a process function');
    });

    module('Process');

    QUnit.test('The processor returns a single integer', function(assert){
        QUnit.expect(1);

        var expectedResult = {
            cardinality : 'single',
            baseType : 'integer',
            value : 2
        };
        randomIntegerProcessor.preProcessor = preProcessorFactory({});
        randomIntegerProcessor.expression = {
            attributes : { min: 2, max : 2, step : 1 }
        };

	    assert.deepEqual(randomIntegerProcessor.process(), expectedResult, 'The processor result is a single integer');
    });

    QUnit.asyncTest('Fails if there aren\'t any attributes', function(assert){
        QUnit.expect(1);
        randomIntegerProcessor.expression = {
            attributes : {  }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Without the attributes the processor throws and error');
            QUnit.start();
        });

	    randomIntegerProcessor.process();
    });

    QUnit.asyncTest('Fails if max is not valid', function(assert){
        QUnit.expect(1);
        randomIntegerProcessor.expression = {
            attributes : { max : 'foo' }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'The max attribute must have a value');
            QUnit.start();
        });

	    randomIntegerProcessor.process();
    });

    QUnit.asyncTest('Fails if min is greater than max', function(assert){
        QUnit.expect(1);
        randomIntegerProcessor.expression = {
            attributes : { min : 10, max : 5  }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'The max attribute must be greater than the min');
            QUnit.start();
        });

	    randomIntegerProcessor.process();
    });

    var dataProvider = [{
        title : '1 to 10',
        attributes : {
            min     : 1,
            max     : 10,
            step    : 1
        },
        expectedRange : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    }, {
        title : '0 to 9 with default value',
        attributes : {
            min     : -Infinity,
            max     : 9,
            step    : NaN
        },
        expectedRange : [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    }, {
        title : '2 to 11 with a 3 step',
        attributes : {
            min     : 2,
            max     : 11,
            step    : 3
        },
        expectedRange : [2, 5, 8, 11]
    }, {
        title : '15 to 20 from strings',
        attributes : {
            min     : '17',
            max     : '20'
        },
        expectedRange : [17, 18, 19, 20]
    }];

    QUnit
      .cases(dataProvider)
      .test('randomInteger ', function(data, assert){
        randomIntegerProcessor.expression = {
            attributes : data.attributes
        };
        randomIntegerProcessor.preProcessor = preProcessorFactory({});
        var result = randomIntegerProcessor.process();
        assert.ok( _.contains(data.expectedRange, result.value), 'The value ' + result.value + ' is in the range');
    });

});
