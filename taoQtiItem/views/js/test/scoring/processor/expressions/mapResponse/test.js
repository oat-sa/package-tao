define([
    'lodash',
    'taoQtiItem/scoring/processor/expressions/preprocessor',
    'taoQtiItem/scoring/processor/expressions/mapResponse',
    'taoQtiItem/scoring/processor/errorHandler'
], function(_, preProcessorFactory, mapResponseProcessor, errorHandler){

    module('API');

    QUnit.test('structure', function(assert){
        assert.ok(_.isPlainObject(mapResponseProcessor), 'the processor expose an object');
        assert.ok(_.isFunction(mapResponseProcessor.process), 'the processor has a process function');
    });

    module('Process');

    QUnit.asyncTest('Fails if no variable is found', function(assert){
        QUnit.expect(1);
        mapResponseProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponseProcessor.state = {
            RESPONSE_1 : {
            }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Without the variable in the state it throws and error');
            QUnit.start();
        });

	    mapResponseProcessor.process();
    });

    QUnit.asyncTest('Fails if variable has no mapping', function(assert){
        QUnit.expect(1);
        mapResponseProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponseProcessor.state = {
            RESPONSE : {
                cardinality     : 'single',
                baseType        : 'identifier',
                value           : 'choice-1',
                correctResponse : 'choice-2'
            }
        };
        errorHandler.listen('scoring', function(err){
            assert.equal(err.name, 'Error', 'Without the variable in the state it throws and error');
            QUnit.start();
        });

	    mapResponseProcessor.process();
    });

    var dataProvider = [{
        title : 'single identifier',
        response : {
            cardinality     : 'single',
            baseType        : 'identifier',
            value           : 'choice-1',
            correctResponse : 'choice-2',
            mapping         : {
                qtiClass    : 'mapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-1",
                    mapValue     : "15",
                    attributes: {
                        caseSensitive : false
                    }
                }, {
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-2",
                    mapValue     : "30",
                    attributes: {
                        caseSensitive : false
                    }
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 15
        }
    }, {
       title : 'single string not case sensitive',
        response : {
            cardinality     : 'single',
            baseType        : 'string',
            value           : 'a',
            correctResponse : 'A',
            mapping         : {
                qtiClass    : 'mapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : "mapEntry",
                    mapKey      : "A",
                    mapValue     : "1",
                    attributes: {
                        caseSensitive : false
                    }
                }, {
                    qtiClass    : "mapEntry",
                    mapKey      : "B",
                    mapValue     : "0",
                    attributes: {
                        caseSensitive : false
                    }
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 1
        }
    }, {
       title : 'single string case sensitive',
        response : {
            cardinality     : 'single',
            baseType        : 'string',
            value           : 'a',
            correctResponse : 'A',
            mapping         : {
                qtiClass    : 'mapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : "mapEntry",
                    mapKey      : "A",
                    mapValue     : "1",
                    attributes: {
                        caseSensitive : true
                    }
                }, {
                    qtiClass    : "mapEntry",
                    mapKey      : "B",
                    mapValue     : "0",
                    attributes: {
                        caseSensitive : true
                    }
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }
    }, {
        title : 'single identifier defaultValue',
        response : {
            cardinality     : 'single',
            baseType        : 'identifier',
            value           : 'choice-3',
            correctResponse : 'choice-2',
            mapping         : {
                qtiClass    : 'mapping',
                attributes  : {},
                mapEntries  : [{
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-1",
                    mapValue     : "15",
                    attributes: {
                        caseSensitive : false
                    }
                }, {
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-2",
                    mapValue     : "30",
                    attributes: {
                        caseSensitive : false
                    }
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 0
        }
    }, {
        title : 'single identifier lowerBound',
        response : {
            cardinality     : 'single',
            baseType        : 'identifier',
            value           : 'choice-3',
            correctResponse : 'choice-2',
            mapping         : {
                qtiClass    : 'mapping',
                attributes  : {
                    lowerBound : 50
                },
                mapEntries  : [{
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-1",
                    mapValue     : "15",
                    attributes: {
                        caseSensitive : false
                    }
                }, {
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-2",
                    mapValue     : "30",
                    attributes: {
                        caseSensitive : false
                    }
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 50
        }
    }, {
        title : 'single identifier upperBound',
        response : {
            cardinality     : 'single',
            baseType        : 'identifier',
            value           : 'choice-2',
            correctResponse : 'choice-2',
            mapping         : {
                qtiClass    : 'mapping',
                attributes  : {
                    upperBound : 5
                },
                mapEntries  : [{
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-1",
                    mapValue     : "15",
                    attributes: {
                        caseSensitive : false
                    }
                }, {
                    qtiClass    : "mapEntry",
                    mapKey      : "choice-2",
                    mapValue     : "30",
                    attributes: {
                        caseSensitive : false
                    }
                }]
            }
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 5
        }
    }, {
        title : 'multiple directedPairs',
        response : {
            cardinality     : 'multiple',
            baseType        : 'directedPair',
            value           : [['C', 'R'], ['D', 'M']],
            correctResponse : [ "C R", "D M", "L M", "P T" ],
            mapping: {
                qtiClass: "mapping",
                attributes: { defaultValue: 0 },
                mapEntries : [{
                    qtiClass: "mapEntry",
                    mapKey: "C R",
                    mapValue : "1",
                    attributes: { caseSensitive : false }
                },{
                    qtiClass: "mapEntry",
                    mapKey: "D M",
                    mapValue : "0.5",
                    attributes: { caseSensitive : false }
                },{
                    qtiClass: "mapEntry",
                    mapKey: "L M",
                    mapValue : "0.5",
                    attributes: { caseSensitive : false }
                },{
                    qtiClass: "mapEntry",
                    mapKey: "P T",
                    mapValue : "1",
                    attributes: { caseSensitive : false }
                }]
            },
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 1.5
        }
   }, {
        title : 'multiple strings multiple response',
        response : {
            cardinality     : 'multiple',
            baseType        : 'string',
            value           : ['B', 'B', 'C'],
            correctResponse : ['B', 'C'],
            mapping: {
                qtiClass: "mapping",
                attributes: { defaultValue: 0 },
                mapEntries : [{
                    qtiClass: "mapEntry",
                    mapKey: "A",
                    mapValue : "0",
                    attributes: { caseSensitive : false }
                },{
                    qtiClass: "mapEntry",
                    mapKey: "B",
                    mapValue : "1",
                    attributes: { caseSensitive : true }
                },{
                    qtiClass: "mapEntry",
                    mapKey: "C",
                    mapValue : "0.5",
                    attributes: { caseSensitive : false }
                },{
                    qtiClass: "mapEntry",
                    mapKey: "D",
                    mapValue : "0",
                    attributes: { caseSensitive : false }
                }]
            },
        },
        expectedResult : {
            cardinality : 'single',
            baseType    : 'float',
            value       : 1.5
        }

    }];

    QUnit
      .cases(dataProvider)
      .test('mapResponse ', function(data, assert){
        var state = {
            RESPONSE : data.response
        };

        mapResponseProcessor.expression = {
            attributes : { identifier : 'RESPONSE' }
        };
        mapResponseProcessor.state = state;
        mapResponseProcessor.preProcessor = preProcessorFactory(state);
        assert.deepEqual(mapResponseProcessor.process(), data.expectedResult, 'The map response is correct');
    });
});
