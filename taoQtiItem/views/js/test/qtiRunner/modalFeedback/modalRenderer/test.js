define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiRunner/modalFeedback/modalRenderer',
    'json!taoQtiItem/test/samples/json/inlineModalFeedback.json'
], function($, _, Element, QtiLoader, QtiRenderer, containerHelper, modalRenderer, itemData){

    var containerId = '#item-container';
    var item;

    QUnit.module('Modal Feedback rendering', {
        teardown : function(){
            if(item instanceof Element){
                item.unset();
                containerHelper.clear();
            }
        }
    });

    var testCases = [
        {
            title : 'choice interaction',
            itemSession : {
                FEEDBACK_1 : {base : {identifier : 'feedbackModal_1'}},
                FEEDBACK_3 : {base : {identifier : 'feedbackModal_3'}}
            },
            feedbacks : [
                {
                    identifier : 'feedbackModal_1',
                    title : 'modal feedback title',
                    text : 'right',
                    style : 'positive'
                },
                {
                    identifier : 'feedbackModal_3',
                    title : '',
                    text : 'thiss is right',
                    style : ''
                }
            ]
        },
        {
            title : 'choice & order interactions',
            itemSession : {
                FEEDBACK_2 : {base : {identifier : 'feedbackModal_2'}},
                FEEDBACK_4 : {base : {identifier : 'feedbackModal_4'}},
                FEEDBACK_5 : {base : {identifier : 'feedbackModal_5'}}//feedbackModal_5 has the same content as the feedbackModal_4 so it won't be displayed
            },
            feedbacks : [
                {
                    identifier : 'feedbackModal_2',
                    title : 'modal feedback title',
                    text : 'wrong',
                    style : 'negative'
                },
                {
                    identifier : 'feedbackModal_4',
                    title : '',
                    text : 'Correct',
                    style : 'positive'
                }
            ]
        },
        {
            title : 'choice & inline interactions',
            itemSession : {
                FEEDBACK_1 : {base : {identifier : 'feedbackModal_1'}},
                FEEDBACK_3 : {base : {identifier : 'feedbackModal_3'}},
                FEEDBACK_6 : {base : {identifier : 'feedbackModal_6'}},
                FEEDBACK_7 : {base : {identifier : 'feedbackModal_7'}}, //feedback #6 and #7 have the same title and text but even with different style, only the first one shall be displayed
                FEEDBACK_8 : {base : {identifier : 'feedbackModal_8'}},
                FEEDBACK_9 : {base : {identifier : 'feedbackModal_9'}}//feedback #9 and #7 have the same title, text and style. The are related to inline iteractions that are both in the same block so contaier, so only the first one #7 will be displayed
            },
            feedbacks : [
                {
                    identifier : 'feedbackModal_1',
                    title : 'modal feedback title',
                    text : 'right',
                    style : 'positive'
                },
                {
                    identifier : 'feedbackModal_3',
                    title : '',
                    text : 'thiss is right',
                    style : ''
                },
                {
                    identifier : 'feedbackModal_6',
                    title : '',
                    text : 'correct',
                    style : 'positive'
                },
                {
                    identifier : 'feedbackModal_8',
                    title : 'modal feedback title',
                    text : 'Some feedback text.',
                    style : ''
                }
            ]
        }
    ];

    QUnit.cases(testCases)
        .asyncTest('renders an item', function(testCase, assert){

            var renderer = new QtiRenderer({baseUrl : './'});

            new QtiLoader().loadItemData(itemData, function(_item){
                var loader = this;
                renderer.load(function(){

                    var result, $result, count;

                    item = _item;
                    item.setRenderer(this);

                    result = item.render({});

                    assert.ok(typeof result === 'string', 'The renderer creates a string');
                    assert.ok(result.length > 0, 'The renderer create some output');

                    $result = $(result);

                    var $choiceInteraction = $('.qti-choiceInteraction', $result);
                    var $orderInteraction = $('.qti-orderInteraction', $result);
                    var $textEntryInteraction = $('.qti-textEntryInteraction', $result);
                    var $inlineChoiceInteraction = $('.qti-inlineChoiceInteraction', $result);
                    var $inlineInteractionContainer = $inlineChoiceInteraction.parent('.col-12');

                    assert.ok($result.hasClass('qti-item'), 'The result is a qti item');
                    assert.equal($('.qti-itemBody', $result).length, 1, 'The result contains an item body');
                    assert.equal($choiceInteraction.length, 1, 'The result contains a choice interaction');
                    assert.equal($orderInteraction.length, 1, 'The result contains an order interaction');
                    assert.equal($textEntryInteraction.length, 1, 'The result contains a text enry interaction');
                    assert.equal($inlineChoiceInteraction.length, 1, 'The result contains an inline choice interaction');
                    assert.equal($inlineInteractionContainer.length, 1, 'Inline interaction container found');
                    assert.equal($('.qti-modalFeedback', $result).length, 0, 'no modal feedback yet');

                    //render in dom
                    $(containerId).append($result);
                    count = modalRenderer.showFeedbacks(item, loader, renderer, testCase.itemSession, _.noop, function(){

                        QUnit.start();
                        assert.equal($('.qti-modalFeedback', $result).length, testCase.feedbacks.length, 'feedback modal rendered');

                        _.each(testCase.feedbacks, function(fb){

                            var $feedback = $result.find('[data-identifier=' + fb.identifier + ']');
                            assert.equal($feedback.length, 1, 'found feedback dom element for ' + fb.identifier);
                            if(fb.style){
                                assert.ok($feedback.hasClass(fb.style), 'style class correctly set');
                            }else{
                                assert.equal($feedback.attr('class').trim(), 'modal qti-modalFeedback', 'the unique css class must be qti-modalFeedback');
                            }

                            if(fb.title){
                                assert.equal($feedback.children('.qti-title').length, 1, 'title found');
                                assert.equal($feedback.children('.qti-title').text(), fb.title, 'title text ok');
                            }else{
                                assert.equal($feedback.children('.qti-title').length, 0, 'no title');
                            }
                            assert.equal($feedback.find('.modal-body').length, 1, 'feedback body found');
                            assert.equal($feedback.find('.modal-body').text().trim(), fb.text, 'feedback body found');
                        });

                    });

                }, loader.getLoadedClasses());
            });
        });

});

