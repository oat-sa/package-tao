define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/mapResponsePoint',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, preProcessorFactory, mapResponsePointProcessor, errorHandler){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(mapResponsePointProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(mapResponsePointProcessor.process), 'the processor has a process function');
    });

    module('Process');

    QUnit.asyncTest('Fails if no variable is found', function(assert){
        QUnit.expect(1);
        mapResponsePointProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponsePointProcessor.state = {
            RESPONSE_1 : {
            }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Without the variable in the state it throws and error');
            QUnit.start();
        });

	    mapResponsePointProcessor.process();
    });

    QUnit.asyncTest('Fails if variable has no mapping', function(assert){
        QUnit.expect(1);
        mapResponsePointProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponsePointProcessor.state = {
            RESPONSE : {
                cardinality     : 'single',
                baseType        : 'point',
                value           : '12 12'
            }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Without a mapping in the variable variable in the state it throws and error');
            QUnit.start();
        });

	    mapResponsePointProcessor.process();
    });

    QUnit.asyncTest('Fails if the variable is not a point', function(assert){
        QUnit.expect(1);
        mapResponsePointProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponsePointProcessor.state = {
            RESPONSE : {
                cardinality     : 'single',
                baseType        : 'float',
                value           : '12.5',
                mapping         : {
                    qtiClass : 'areaMapping'
                }
            }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'The variable must be of type point');
            QUnit.start();
        });

	    mapResponsePointProcessor.process();
    });

    var dataProvider = [{
        title : 'in a rectangle',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '15 15',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '100'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 100
        }
    }, {
        title : 'out a rectangle',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '30 30',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '100'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }
    }, {
        title : 'in a circle',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '20 20',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'circle',
                    coords      : '10,10,20',
                    mappedValue : '2.5'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 2.5
        }
    }, {
        title : 'out a circle',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '20 20',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'circle',
                    coords      : '10,10,5',
                    mappedValue : '2.5'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }
    }, {
        title : 'in a simple poly',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '134 70',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'poly',
                    coords      : '75,10,146,79,52,132,9,51',
                    mappedValue : '11.75'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 11.75
        }
    }, {
        title : 'out a simple poly',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '134 65',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'poly',
                    coords      : '75,10,146,79,52,132,9,51',
                    mappedValue : '12'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }
    }, {
        title : 'in a complex poly',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '255 411',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'poly',
                    coords      : '291,173,249,414,629,427,557,174,423,569,126,280,431,260',
                    mappedValue : '0.75'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0.75
        }
    }, {
        title : 'out a complex poly',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '380 331',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'poly',
                    coords      : '291,173,249,414,629,427,557,174,423,569,126,280,431,260',
                    mappedValue : '0.25'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }
      }, {
        title : 'in an ellipse',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '9 12',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'ellipse',
                    coords      : '57,18,55,14',
                    mappedValue : '0.25'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0.25
        }
      }, {
        title : 'out an ellipse',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '10 8',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'ellipse',
                    coords      : '57,18,55,14',
                    mappedValue : '0.75'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }

    }, {
        title : 'multiple points in a rectangle',
        response : {
            cardinality     : 'multiple',
            baseType        : 'point',
            value           : ['15 15', '16 16'],
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '2'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 2
        }
    }, {
        title : 'multiple points in multiple rectangles',
        response : {
            cardinality     : 'multiple',
            baseType        : 'point',
            value           : ['15 15', '75 75'],
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '11'
                }, {
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '60,60,90,90',
                    mappedValue : '13'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 24
        }
     }, {
        title : 'lowerBound',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '15 15',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {
                    lowerBound : '0.75'
                },
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '0.25'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0.75
        }
     }, {
        title : 'upperBound',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '15 15',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {
                    upperBound : '3'
                },
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '3.25'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 3
        }
     }, {
        title : 'defaultValue',
        response : {
            cardinality     : 'single',
            baseType        : 'point',
            value           : '1 1',
            mapping         : {
                qtiClass    : 'areaMapping',
                attributes  : {
                    defaultValue : '12'
                },
                mapEntries  : [{
                    qtiClass    : 'areaMapEntry',
                    shape       : 'rect',
                    coords      : '10,10,20,20',
                    mappedValue : '54'
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 12
        }
    }];


    QUnit
      .cases(dataProvider)
      .test('mapResponsePoint ', function(data, assert){
        var state = {
            RESPONSE : data.response
        };

        mapResponsePointProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponsePointProcessor.state = state;
        mapResponsePointProcessor.preProcessor = preProcessorFactory(state);
        assert.deepEqual(mapResponsePointProcessor.process(), data.expectedResult, 'The map response is correct');
    });

});
