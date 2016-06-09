define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/history.json'
], function($, _, qtiItemRunner, orderData){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    module('Order Interaction', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('renders correclty', function(assert){
        QUnit.expect(18);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('.qti-interaction.qti-orderInteraction').length, 1, 'the container contains a choice interaction .qti-orderInteraction');
                assert.equal($container.find('.qti-orderInteraction .qti-prompt-container').length, 1, 'the interaction contains a prompt');
                assert.equal($container.find('.qti-orderInteraction .instruction-container').length, 1, 'the interaction contains a instruction box');
                assert.equal($container.find('.qti-orderInteraction .choice-area').length, 1, 'the interaction contains a choice list');
                assert.equal($container.find('.qti-orderInteraction .qti-choice').length, 5, 'the interaction has 5 choices');
                assert.equal($container.find('.qti-orderInteraction .result-area').length, 1, 'the interaction contains a result area');

                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'periods-of-history', 'the .qti-item node has the right identifier');

                assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(1)').data('identifier'), 'Prehistory', 'the 1st choice has the right identifier');
                assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(2)').data('identifier'), 'Antiquity', 'the 2nd choice has the right identifier');
                assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(3)').data('identifier'), 'MiddleAges', 'the 3rd choice has the right identifier');
                assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(4)').data('identifier'), 'ModernEra', 'the 4th choice has the right identifier');
                assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(5)').data('identifier'), 'ContemporaryEra', 'the 5th choice has the right identifier');

                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to select a choice', function(assert){
        QUnit.expect(10);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-orderInteraction').length, 1, 'the container contains a choice interaction .qti-orderInteraction');
                assert.equal($container.find('.qti-orderInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                var $prehistory = $('.qti-choice[data-identifier="Prehistory"]', $container);
                assert.equal($prehistory.length, 1, 'the Prehistory choice exists');

                $prehistory.trigger('mousedown');
            })
            .on('statechange', function(state){

                assert.equal($container.find('.qti-orderInteraction .choice-area .qti-choice').length, 4, 'the choice list contains now 4 choices');
                assert.equal($container.find('.qti-orderInteraction .result-area .qti-choice').length, 1, 'the result list contains now 1 choice');

                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE, { response : { list  : { identifier : ['Prehistory'] } } }, 'The Prehistory response is selected');
                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to reorder choices', function(assert){
        QUnit.expect(12);

        var $container = $('#' + fixtureContainerId);
        var changes = 0;

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-orderInteraction').length, 1, 'the container contains a choice interaction .qti-orderInteraction');
                assert.equal($container.find('.qti-orderInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                var $prehistory = $('.qti-choice[data-identifier="Prehistory"]', $container);
                assert.equal($prehistory.length, 1, 'the Prehistory choice exists');

                var $antiquity = $('.qti-choice[data-identifier="Antiquity"]', $container);
                assert.equal($antiquity.length, 1, 'the Antiquity choice exists');

                $prehistory.trigger('mousedown');

                _.delay(function(){
                    $antiquity.trigger('mousedown');

                    _.delay(function(){
                        assert.equal($container.find('.qti-orderInteraction .choice-area .qti-choice').length, 3, 'the choice list contains now 3 choices');
                        assert.equal($container.find('.qti-orderInteraction .result-area .qti-choice').length, 2, 'the result list contains now 2 choice');
                        $antiquity.trigger('mousedown');

                        _.delay(function(){

                            assert.ok($antiquity.hasClass('active'), 'The antiquity choice is now active');

                            $('.icon-move-before').trigger('mousedown');
                        }, 10);
                    }, 10);
                }, 10);
            })
            .on('statechange', function(state){
                if(++changes === 3){
                    assert.ok(typeof state === 'object', 'The state is an object');
                    assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                    assert.deepEqual(state.RESPONSE, { response : { list  : { identifier : ['Antiquity', 'Prehistory'] } } }, 'The response follows the reordering');
                    QUnit.start();
                }
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('set the default response', function(assert){
        QUnit.expect(6);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){

                assert.equal($container.find('.qti-orderInteraction .choice-area .qti-choice').length, 5, 'the choice list contains all choices');
                assert.equal($container.find('.qti-orderInteraction .result-area .qti-choice').length, 0, 'the result list contains no choices');

                this.setState({ RESPONSE : { response : { list  : { identifier : ['Antiquity', 'Prehistory'] } } } });

                _.delay(function(){
                    assert.equal($container.find('.qti-orderInteraction .choice-area .qti-choice').length, 3, 'the choice list contains now 3 choices');
                    assert.equal($container.find('.qti-orderInteraction .result-area .qti-choice').length, 2, 'the result list contains now 2 choices');

                    QUnit.start();
                }, 10);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('destroys', function(assert){
        QUnit.expect(4);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){
                var self = this;

                //call destroy manually
                var interaction = this._item.getInteractions()[0];
                interaction.renderer.destroy(interaction);

                var $prehistory = $('.qti-choice[data-identifier="Prehistory"]', $container);
                assert.equal($prehistory.length, 1, 'the Prehistory choice exists');

                $prehistory.trigger('mousedown');

                _.delay(function(){

                    assert.deepEqual(self.getState(), {'RESPONSE': { response : { list : { identifier : [] } } } }, 'Click does not trigger response once destroyed');

                    QUnit.start();
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('resets the response', function(assert){
        QUnit.expect(9);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-orderInteraction').length, 1, 'the container contains a choice interaction .qti-orderInteraction');
                assert.equal($container.find('.qti-orderInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                var $prehistory = $('.qti-choice[data-identifier="Prehistory"]', $container);
                assert.equal($prehistory.length, 1, 'the Prehistory choice exists');

                $prehistory.trigger('mousedown');

                _.delay(function(){
                    assert.equal($container.find('.qti-orderInteraction .choice-area .qti-choice').length, 4, 'the choice list contains now 4 choices');
                    assert.equal($container.find('.qti-orderInteraction .result-area .qti-choice').length, 1, 'the result list contains now 1 choice');

                    //call destroy manually
                    var interaction = self._item.getInteractions()[0];
                    interaction.renderer.resetResponse(interaction);

                    _.delay(function(){

                        assert.equal($container.find('.qti-orderInteraction .choice-area .qti-choice').length, 5, 'the choice list contains all choices');
                        assert.equal($container.find('.qti-orderInteraction .result-area .qti-choice').length, 0, 'the result list contains no choices anymore');

                        QUnit.start();
                    }, 100);
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('restores order of shuffled choices', function(assert){
        QUnit.expect(9);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        //hack the item data to set the shuffle attr to true
        var shuffled = _.cloneDeep(orderData);
        shuffled.body.elements.interaction_orderinteraction_547481ffc8c1b803673841.attributes.shuffle = true;

        runner = qtiItemRunner('qti', shuffled)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-orderInteraction').length, 1, 'the container contains a choice interaction .qti-orderInteraction');
                assert.equal($container.find('.qti-orderInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                this.setState({
                    RESPONSE : {
                        response : { list : { identifier : [] } },
                        order : ['ContemporaryEra', 'Antiquity', 'ModernEra', 'MiddleAges', 'Prehistory']
                    }
                });

                _.delay(function(){

                    assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(1)').data('identifier'), 'ContemporaryEra', 'the 1st choice has the right identifier');
                    assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(2)').data('identifier'), 'Antiquity', 'the 2nd choice has the right identifier');
                    assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(3)').data('identifier'), 'ModernEra', 'the 3rd choice has the right identifier');
                    assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(4)').data('identifier'), 'MiddleAges', 'the 4th choice has the right identifier');
                    assert.equal($container.find('.qti-orderInteraction .qti-choice:nth-child(5)').data('identifier'), 'Prehistory', 'the 5th choice has the right identifier');

                    QUnit.start();
                }, 100);
            })
            .init()
            .render($container);
    });

    module('Visual Test');

    QUnit.asyncTest('Display and play', function(assert){
        QUnit.expect(4);

        var $container = $('#' + outsideContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', orderData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-orderInteraction').length, 1, 'the container contains a choice interaction .qti-orderInteraction');
                assert.equal($container.find('.qti-orderInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

