define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/correct',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, preProcessorFactory, correctProcessor, errorHandler){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(correctProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(correctProcessor.process), 'the processor has a process function');
    });

    module('Process');


    QUnit.asyncTest('Fails if no variable is found', function(assert){
        QUnit.expect(1);
        correctProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        correctProcessor.state = {
            RESPONSE_1 : {
                cardinality         : 'single',
                baseType            : 'identifier',
                correctResponse     : 'choice-1',
                mapping             : {},
                value               : 'choice-2'
            }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Without the variable in the state it throws and error');
            QUnit.start();
        });

	    correctProcessor.process();
    });

    var dataProvider = [{
        title : 'single identifier',
        response : {
            cardinality     : 'single',
            baseType        : 'identifier',
            value           : 'choice-2',
            correctResponse : 'choice-1'
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'identifier',
            value       : 'choice-1'
        }
    }, {
        title : 'multiple integers',
        response : {
            cardinality     : 'multiple',
            baseType        : 'integer',
            value           : [4, 5],
            correctResponse : ['1', '2']
        },
        expectedResult : {
            cardinality : 'multiple',
            baseType    : 'integer',
            value       : [1, 2]
        }
    }, {
        title : 'null',
        response : null,
        expectedResult : null
    }, {
        title : 'multiple directedPairs',
        response : {
            cardinality     : 'multiple',
            baseType        : 'directedPair',
            value           : [['C', 'R'], ['D', 'M']],
            correctResponse : [
                "C R",
                "D M",
                "L M",
                "P T"
            ],
        },
        expectedResult : {
            cardinality : 'multiple',
            baseType    : 'directedPair',
            value       : [
                ['C', 'R'],
                ['D', 'M'],
                ['L', 'M'],
                ['P', 'T']
            ]
        }
    }];

    QUnit
      .cases(dataProvider)
      .test('correct ', function(data, assert){
        var state = {
            RESPONSE : data.response
        };
        correctProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        correctProcessor.state = state;
        correctProcessor.preProcessor = preProcessorFactory(state);
        assert.deepEqual(correctProcessor.process(), data.expectedResult, 'The results match correct');
    });


});
