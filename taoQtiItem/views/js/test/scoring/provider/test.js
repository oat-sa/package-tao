define([
    'lodash',
    'taoItems/scoring/api/scorer',
    'taoQtiItem/scoring/provider/qti',
    'json!taoQtiItem/test/samples/json/space-shuttle.json',
    'json!taoQtiItem/test/samples/json/space-shuttle-m.json',
    'json!taoQtiItem/test/samples/json/characters.json',
    'json!taoQtiItem/test/samples/json/edinburgh.json',
    'json!taoQtiItem/test/samples/json/customrp/Choicemultiple_2014410822.json',
    'json!taoQtiItem/test/samples/json/customrp/TextEntrynumeric_770468849.json',
    'json!taoQtiItem/test/samples/json/customrp/Choicemultiple_871212949.json',
    'json!taoQtiItem/test/samples/json/customrp/Matchsingle_143114773.json',
    'json!taoQtiItem/test/samples/json/customrp/order.json',
    'json!taoQtiItem/test/samples/json/es6.json',
    'json!taoQtiItem/test/samples/json/pluto.json',
], function(_, scorer, qtiScoringProvider,
        singleCorrectData,
        multipleCorrectData,
        multipleMapData,
        singleMapPointData,
        customChoiceMultipleData,
        customTextEntryNumericData,
        customChoiceMultipleData2,
        customChoiceSingleData,
        orderData,
        multipleResponseCorrectData,
        embedConditionsData
){
    'use strict';

    QUnit.module('Provider API');

    QUnit.test('module', function(assert){
        assert.ok(typeof qtiScoringProvider !== 'undefined', "The module exports something");
        assert.ok(typeof qtiScoringProvider === 'object', "The module exports an object");
        assert.ok(typeof qtiScoringProvider.process === 'function', "The module exposes a process method");
    });


    QUnit.module('Register the provider', {
        teardown : function(){
            //reset the provider
            scorer.providers = undefined;
        }
    });

    QUnit.test('register the qti provider', function(assert){
        QUnit.expect(4);

        assert.ok(typeof scorer.providers === 'undefined', 'the scorer has: no providers');

        scorer.register('qti', qtiScoringProvider);

        assert.ok(typeof scorer.providers === 'object', 'the scorer has now providers');
        assert.ok(typeof scorer.providers.qti === 'object', 'the scorer has now the qti providers');
        assert.equal(scorer.providers.qti, qtiScoringProvider, 'the scorer has now the qti providers');

    });

    QUnit.module('Without processing', {
        teardown : function(){
            //reset the provides
            scorer.providers = undefined;
        }
    });

    QUnit.asyncTest('default outcomes', function(assert){
        QUnit.expect(5);

        var responses =   {
            "RESPONSE": {
                "base": {
                    "identifier": "Discovery"
                }
            }
        };

        var noRulesItemData = _.cloneDeep(singleCorrectData);
        noRulesItemData.responseProcessing.responseRules = [];

        scorer.register('qti', qtiScoringProvider);

        scorer('qti')
            .on('error', function(err){
                assert.ok(false, 'Got an error : ' + err);
            })
            .on('outcome', function(outcomes){

                assert.ok(typeof outcomes === 'object', "the outcomes are an object");
                assert.ok(typeof outcomes.RESPONSE === 'object', "the outcomes contains the response");
                assert.deepEqual(outcomes.RESPONSE, responses.RESPONSE, "the response is the same");
                assert.ok(typeof outcomes.SCORE === 'object', "the outcomes contains the score");
                assert.deepEqual(outcomes.SCORE, { base : { integer : 0 } }, "the score has the default value");

                QUnit.start();
            })
            .process(responses, noRulesItemData);
    });

    QUnit.asyncTest('No responseProcessing', function(assert){
        QUnit.expect(2);

        var noRPItemData = _.cloneDeep(singleCorrectData);
        delete noRPItemData.responseProcessing;

        scorer.register('qti', qtiScoringProvider);

        scorer('qti')
            .on('error', function(err){
                assert.ok(err instanceof Error, 'Got an Error');
                assert.equal(err.message, 'The given item has not responseProcessing', 'The error is about responseProcessing');
                QUnit.start();
            })
            .process({}, noRPItemData);
    });

    QUnit.module('Provider process correct template', {
        teardown : function(){
            //reset the provides
            scorer.providers = undefined;
        }
    });

    var tplDataProvider = [{
        title   : 'match correct single identifier',
        item    : singleCorrectData,
        resp    : { base : { identifier: "Atlantis" } },
        score   : { base : { integer : 1 } }
    }, {
        title   : 'match incorrect single identifier',
        item    : singleCorrectData,
        resp    : { base : { identifier: "Discovery" } },
        score   : { base : { integer : 0 } }
    }, {
        title   : 'match correct multiple identifier',
        item    : multipleCorrectData,
        resp    : { list : { identifier: ["Pathfinder", "Atlantis"] } },
        score   : { base : { integer : 1 } }
    }, {
        title   : 'match incorrect multiple identifier',
        item    : multipleCorrectData,
        resp    : { list : { identifier: ["Atlantis", "Discovery"] } },
        score   : { base : { integer : 0 } }
    }, {
        title   : 'map response multiple directedPair',
        item    : multipleMapData,
        resp    : { list : { directedPair:  [['C', 'R'], ['D', 'M']] } },
        score   : { base : { float : 1.5 } }
    }, {
        title   : 'incorrect map response multiple directedPair',
        item    : multipleMapData,
        resp    : { list : { directedPair:  [['M', 'D'], ['R', 'M']] } },
        score   : { base : { float : 0 } }
    }, {
        title   : 'incorrect map response multiple directedPair',
        item    : multipleMapData,
        resp    : { list : { directedPair:  [['M', 'D'], ['R', 'M']] } },
        score   : { base : { float : 0 } }
    }, {
        title   : 'map response  point inside',
        item    : singleMapPointData,
        resp    : { base : { point:  [102, 113] } },
        score   : { base : { float : 1 } }
    }, {
        title   : 'map response  point outside',
        item    : singleMapPointData,
        resp    : { base : { point:  [145, 190] } },
        score   : { base : { float : 0 } }
    }];

    QUnit
        .cases(tplDataProvider)
        .asyncTest('process ', function(data, assert){

            var responses = {
                'RESPONSE' : data.resp
            };

            scorer.register('qti', qtiScoringProvider);

            scorer('qti')
                .on('error', function(err){
                    assert.ok(false, 'Got an error : ' + err);
                })
                .on('outcome', function(outcomes){

                    assert.ok(typeof outcomes === 'object', "the outcomes are an object");
                    assert.ok(typeof outcomes.RESPONSE === 'object', "the outcomes contains the response");
                    assert.deepEqual(outcomes.RESPONSE, responses.RESPONSE, "the response is the same");
                    assert.ok(typeof outcomes.SCORE === 'object', "the outcomes contains the score");
                    assert.deepEqual(outcomes.SCORE, data.score, "the score has the correct value");

                    QUnit.start();
                })
                .process(responses, data.item);
        });

    QUnit.asyncTest('process multiple responses', function(assert){

            QUnit.expect(7);

            var responses = {
                'RESPONSE' : { list : { identifier : ["choice_3"] } },
                'RESPONSE_1' : { base : { identifier : "choice_7" } },
            };
            scorer.register('qti', qtiScoringProvider);

            scorer('qti')
                .on('error', function(err){
                    assert.ok(false, 'Got an error : ' + err);
                })
                .on('outcome', function(outcomes){

                    assert.ok(typeof outcomes === 'object', "the outcomes are an object");
                    assert.ok(typeof outcomes.RESPONSE === 'object', "the outcomes contains the response");
                    assert.deepEqual(outcomes.RESPONSE, responses.RESPONSE, "the response is the same");
                    assert.ok(typeof outcomes.RESPONSE_1 === 'object', "the outcomes contains the response");
                    assert.deepEqual(outcomes.RESPONSE_1, responses.RESPONSE_1, "the response is the same");
                    assert.ok(typeof outcomes.SCORE === 'object', "the outcomes contains the score");
                    assert.deepEqual(outcomes.SCORE, { base : { float : 2 } }, "the score has the correct value");

                    QUnit.start();
                })
                .process(responses, multipleResponseCorrectData);
    });

    QUnit.asyncTest('process multiple responses one is empty', function(assert){

            QUnit.expect(7);

            var responses = {
                'RESPONSE' : { list : { identifier : ["choice_3"] } },
                'RESPONSE_1' : { base : null }
            };
            scorer.register('qti', qtiScoringProvider);

            scorer('qti')
                .on('error', function(err){
                    assert.ok(false, 'Got an error : ' + err);
                })
                .on('outcome', function(outcomes){

                    assert.ok(typeof outcomes === 'object', "the outcomes are an object");
                    assert.ok(typeof outcomes.RESPONSE === 'object', "the outcomes contains the response");
                    assert.deepEqual(outcomes.RESPONSE, responses.RESPONSE, "the response is the same");
                    assert.ok(typeof outcomes.RESPONSE_1 === 'object', "the outcomes contains the response");
                    assert.deepEqual(outcomes.RESPONSE_1, responses.RESPONSE_1, "the response is the same");
                    assert.ok(typeof outcomes.SCORE === 'object', "the outcomes contains the score");
                    assert.deepEqual(outcomes.SCORE, { base : { float : 1 } }, "the score has the correct value");

                    QUnit.start();
                })
                .process(responses, multipleResponseCorrectData);
    });

    QUnit.module('Custom template', {
        teardown : function(){
            //reset the provides
            scorer.providers = undefined;
        }
    });


    var customDataProvider = [{
        title   : 'choice multiple correct',
        item    : customChoiceMultipleData,
        resp    : { RESPONSE_13390220 : { list : { identifier: ['choice_693643701', 'choice_853818748'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 1 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'correct' } }
        }
    }, {
        title   : 'choice multiple incorrect',
        item    : customChoiceMultipleData,
        resp    : { RESPONSE_13390220 : { list : { identifier: ['choice_853818748'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 0 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'incorrect' } }
        }
    }, {
        title   : 'single numeric text entry exact',
        item    : customTextEntryNumericData,
        resp    : { RESPONSE_1 : { base : { float: 4.136 } } },
        outcomes : {
            SCORE : { base : { 'float' : 1 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'correct' } }
        }
    }, {
        title   : 'single numeric text entry pretty correct',
        item    : customTextEntryNumericData,
        resp    : { RESPONSE_1 : { base : { float: 4.132 } } },
        outcomes : {
            SCORE : { base : { 'float' : 1 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'correct' } }
        }
    }, {
        title   : 'single numeric text entry incorrect',
        item    : customTextEntryNumericData,
        resp    : { RESPONSE_1 : { base : { float: 5.8756 } } },
        outcomes : {
            SCORE : { base : { 'float' : 0 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'incorrect' } }
        }
    },{
        title   : 'choice multiple correct',
        item    : customChoiceMultipleData2,
        resp    : { RESPONSE_27966883 : { list : { identifier: ['choice_934383202', 'choice_2022864592','choice_1534527094'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 3 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'correct' } }
        }
    }, {
        title   : 'choice multiple incorrect',
        item    : customChoiceMultipleData2,
        resp    : { RESPONSE_27966883 : { list : { identifier: ['choice_921260236'] } } },
        outcomes : {
            SCORE : { base : { 'float' : -1 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'incorrect' } }
        }
    },{
        title   : 'choice directed pair multiple correct',
        item    : customChoiceSingleData,
        resp    : { RESPONSE : { list : { directedPair: [ 'Match29886762 Match30518135', 'Match5256823 Match2607634', 'Match4430647 Match8604807', 'Match1403839 Match5570831'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 1 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'correct' } }
        }
    }, {
        title   : 'choice directed pair multiple incorrect',
        item    : customChoiceSingleData,
        resp    : { RESPONSE : { list : { directedPair: [ 'Match29886762 Match30518135', 'Match2607634 Match5256823', 'Match4430647 Match8604807', 'Match1403839 Match5570831'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 0 } },
            FEEDBACKBASIC : { base : { 'identifier' : 'incorrect' } }
        }
    },{
        title   : 'ordered correct',
        item    : orderData,
        resp    : { RESPONSE : { list : { identifier: [  'DriverC', 'DriverA', 'DriverB'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 1 } }
        }
    }, {
        title   : 'ordered incorrect',
        item    : orderData,
        resp    : { RESPONSE : { list : { identifier: [  'DriverC', 'DriverAS', 'DriverB'] } } },
        outcomes : {
            SCORE : { base : { 'float' : 0 } }
        }
    }, {
        title   : 'embed conditions correct',
        item    : embedConditionsData.data,
        resp    : {
            RESPONSE   : { base : { identifier: 'choice_1' } },
            RESPONSE_1 : { base : { identifier: 'choice_6' } },
        },
        outcomes : {
            SCORE : { base : { 'float' : 2 } }
        }
    }, {
        title   : 'embed conditions  first correct',
        item    : embedConditionsData.data,
        resp    : {
            RESPONSE   : { base : { identifier: 'choice_1' } },
            RESPONSE_1 : { base : { identifier: 'choice_7' } },
        },
        outcomes : {
            SCORE : { base : { 'float' : 1 } }
        }
    }, {
        title   : 'embed conditions 2nd correct',
        item    : embedConditionsData.data,
        resp    : {
            RESPONSE   : { base : { identifier: 'choice_2' } },
            RESPONSE_1 : { base : { identifier: 'choice_6' } },
        },
        outcomes : {
            SCORE : { base : { 'float' : 0 } }
        }
    }, {
        title   : 'embed conditions 2nd null',
        item    : embedConditionsData.data,
        resp    : {
            RESPONSE   : { base : { identifier: 'choice_1' } },
            RESPONSE_1 : { base : null },
        },
        outcomes : {
            SCORE : { base : { 'float' : 1 } }
        }
    }, {
        title   : 'embed conditions null',
        item    : embedConditionsData.data,
        resp    : {
            RESPONSE   : { base : null },
            RESPONSE_1 : { base : null },
        },
        outcomes : {
            SCORE : { base : { 'float' : 0 } }
        }
    }];

    QUnit
        .cases(customDataProvider)
        .asyncTest('process ', function(data, assert){

            scorer.register('qti', qtiScoringProvider);

            scorer('qti')
                .on('error', function(err){
                    assert.ok(false, 'Got an error : ' + err);
                })
                .on('outcome', function(outcomes){

                    assert.ok(typeof outcomes === 'object', "the outcomes are an object");

                    _.forEach(data.outcomes, function(outcome, name){
                        assert.deepEqual(outcomes[name], outcome, "the outcome " + name + " is correct" );
                    });

                    QUnit.start();
                })
                .process(data.resp, data.item);
        });

});

