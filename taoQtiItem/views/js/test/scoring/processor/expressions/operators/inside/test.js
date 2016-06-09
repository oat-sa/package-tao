define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/operators/inside'
], function (_, preProcessorFactory, insideProcessor) {
    "use strict";

    QUnit.module('API');

    QUnit.test('structure', function (assert) {
        assert.ok(_.isPlainObject(insideProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(insideProcessor.process), 'the processor has a process function');
        assert.ok(_.isArray(insideProcessor.operands), 'the processor has a process function');
    });

    QUnit.module('Process');


    var dataProvider = [{
        title: 'rect inside',
        coords: '0,0,10,20',
        shape: 'rect',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '1 1'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'rect outside',
        coords: '0,0,10,20',
        shape: 'rect',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '-21 1'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    }, {
        title: 'poly inside',
        coords: '291,173,249,414,629,427,557,174,423,569,126,280,431,260',
        shape: 'poly',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '255 411'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'poly outside',
        coords: '291,173,249,414,629,427,557,174,423,569,126,280,431,260',
        shape: 'poly',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '1 1'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    }, {
        title: 'default',
        coords: '',
        shape: 'default',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '0 8'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    }, {
        title: 'circle inside',
        coords: '5,5,5',
        shape: 'circle',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '3 3'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    },{
        title: 'circle outside',
        coords: '5,5,5',
        shape: 'circle',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '31 3'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    },{
        title: 'circle',
        coords: '57,18,55,14',
        shape: 'ellipse',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '9 12'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: true
        }
    }, {
        title: 'wrong type',
        coords: '57,18,55,14,57,18,55,14',
        shape: 'cube',

        operands: [{
            cardinality: 'single',
            baseType: 'point',
            value: '9 12'
        }],
        expectedResult: {
            cardinality: 'single',
            baseType: 'boolean',
            value: false
        }
    }, {
        title: 'one null',
        coords: '0,0,10,20',
        shape: 'rect',
        operands: [null],
        expectedResult: null
    }];

    QUnit
        .cases(dataProvider)
        .test('inside ', function (data, assert) {
            insideProcessor.preProcessor = preProcessorFactory({});

            insideProcessor.operands = data.operands;

            insideProcessor.expression = {
                attributes: {
                    coords: data.coords,
                    shape: data.shape
                }
            };

            assert.deepEqual(insideProcessor.process(), data.expectedResult, 'The inside is correct');
        });
});
