define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/mathConstant'
], function(_, mathConstantProcessor){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(mathConstantProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(mathConstantProcessor.process), 'the processor has a process function');
    });

    module('Process');

    var dataProvider = [{
        title : 'pi is Math.PI',
        expression : {
            attributes : { name : 'pi' }
        },
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : Math.PI
        }
    }, {
        title : 'pi value',
        expression : {
            attributes : { name : 'pi' }
        },
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 3.141592653589793
        }
    }, {
        title : 'e is Math.E',
        expression : {
            attributes : { name : 'e' }
        },
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : Math.E
        }
    }, {
        title : 'e value',
        expression : {
            attributes : { name : 'e' }
        },
        expectedResult : {
            cardinality : 'single',
            baseType : 'float',
            value : 2.718281828459045
        }
    }];

    QUnit
      .cases(dataProvider)
      .test('math constant ', function(data, assert){
        mathConstantProcessor.expression = data.expression;
        assert.deepEqual(mathConstantProcessor.process(), data.expectedResult, 'The constant value is correct');
    });
});
