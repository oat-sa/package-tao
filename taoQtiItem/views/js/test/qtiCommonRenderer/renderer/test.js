define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer',
    'json!taoQtiItem/test/samples/json/space-shuttle.json'
], function($, _, QtiLoader, QtiRenderer, itemData){

    var containerId = 'item-container';


    QUnit.module('HTML rendering');

    QUnit.asyncTest('renders an item', function(assert){
        QUnit.expect(5);

        var renderer = new QtiRenderer({ baseUrl : './'});

        new QtiLoader().loadItemData(itemData, function(item){
            renderer.load(function(){
                var result, $result;

                item.setRenderer(this);

                result = item.render({});

                assert.ok(typeof result === 'string', 'The renderer creates a string');
                assert.ok(result.length > 0, 'The renderer create some output');

                $result = $(result);

                assert.ok($result.hasClass('qti-item'), 'The result is a qti item');
                assert.equal($('.qti-itemBody', $result).length, 1, 'The result contains an item body');
                assert.equal($('.qti-choiceInteraction', $result).length, 1, 'The result contains a choice interaction');

                QUnit.start();

            }, this.getLoadedClasses());
        });
    });

    QUnit.asyncTest('renders decorated interactions', function(assert){
        QUnit.expect(8);

        //set up decorator
        var renderer = new QtiRenderer({
            baseUrl : './',
            decorators : {
                before : function preRender(element){
                    if(element.qtiClass === 'choiceInteraction'){
                        return '<p class="pre-choice">pre</p>';
                    }
                },
                after : function postRender(element, qtiSubclass){
                    if(element.qtiClass === 'choiceInteraction'){
                        return '<p class="post-choice">post</p>';
                    }
                },
            }
        });

        new QtiLoader().loadItemData(itemData, function(item){
            renderer.load(function(){
                var result, $result;

                item.setRenderer(this);

                result = item.render({});


                assert.ok(typeof result === 'string', 'The renderer creates a string');
                assert.ok(result.length > 0, 'The renderer create some output');

                $result = $(result);

                assert.ok($result.hasClass('qti-item'), 'The result is a qti item');
                assert.equal($('.qti-choiceInteraction', $result).length, 1, 'The result contains a choice interaction');

                assert.equal($('.pre-choice', $result).length, 1, 'The pre decorator has been inserted');
                assert.ok($('.qti-choiceInteraction', $result).prev().hasClass('pre-choice'), 'The pre decorator is previous the choice interaction');

                assert.equal($('.post-choice', $result).length, 1, 'The post decorator has been inserted');
                assert.ok($('.qti-choiceInteraction', $result).next().hasClass('post-choice'), 'The post decorator is next to the choice interaction');


                QUnit.start();

            }, this.getLoadedClasses());
        });
    });
});

