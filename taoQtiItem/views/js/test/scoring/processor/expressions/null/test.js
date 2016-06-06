define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/null'
], function(_, nullProcessor){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(nullProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(nullProcessor.process), 'the processor has a process function');
    });

    module('Process');

    QUnit.test('null processor', function(assert){
        assert.strictEqual(nullProcessor.process(), null, 'the processor returns null');
    });
});
