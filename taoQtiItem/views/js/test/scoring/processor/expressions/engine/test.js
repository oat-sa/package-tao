define([
    'taoQtiItem/scoring/processor/expressions/engine'
], function(expressionEngineFactory){

    module('API');

    QUnit.asyncTest('factory', function(assert){
        QUnit.expect(1);

        assert.ok(typeof expressionEngineFactory === 'function', 'the engine expose a factory');

        QUnit.start();
    });

    QUnit.asyncTest('engine', function(assert){
        QUnit.expect(2);

        var engine = expressionEngineFactory();

        assert.ok(typeof engine === 'object', 'the engine is an object');
        assert.ok(typeof engine.execute === 'function', 'the engine exposes a execute function');

        QUnit.start();
    });


    module('Parse 1 level tree');

    QUnit.asyncTest('2 operands sum expression', function(assert){
        QUnit.expect(1);

        var expression = {
            qtiClass : 'sum',
            expressions : [{
                qtiClass : "baseValue",
                attributes : {
                    baseType : "integer"
                },
                value : "3"
            },{
                qtiClass : "baseValue",
                attributes : {
                    baseType : "integer"
                },
                value : "7"
            }]
        };

        var expectedResult = {
            cardinality : 'single',
            baseType : 'integer',
            value : 10
        };

        var engine = expressionEngineFactory();

        assert.deepEqual(engine.execute(expression), expectedResult, 'the engine compute the right result');

        QUnit.start();
    });

    module('Parse 2 levels tree');

    QUnit.asyncTest('2 operands sum expression', function(assert){
        QUnit.expect(1);

        var expression = {
            qtiClass : 'subtract',
            expressions : [{
                qtiClass : 'sum',
                expressions : [{
                    qtiClass : "baseValue",
                    attributes : {
                        baseType : "integer"
                    },
                    value : "3"
                },{
                    qtiClass : "baseValue",
                    attributes : {
                        baseType : "integer"
                    },
                    value : "7"
                },{
                    qtiClass : "baseValue",
                    attributes : {
                        baseType : "integer"
                    },
                    value : "5"
                }]
            }, {
                qtiClass : 'product',
                expressions : [{
                    qtiClass : "baseValue",
                    attributes : {
                        baseType : "integer"
                    },
                    value : "2"
                },{
                    qtiClass : "baseValue",
                    attributes : {
                        baseType : "integer"
                    },
                    value : "5"
                }]
            }]
        };

        var expectedResult = {
            cardinality : 'single',
            baseType : 'integer',
            value : 5
        };
        var engine = expressionEngineFactory();
        assert.deepEqual(engine.execute(expression), expectedResult, 'the engine compute the right result');

        QUnit.start();
    });
});

