define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/tao-item.json',
], function($, _, qtiItemRunner, gapMatchData){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    module('GapMatch Interaction', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });


    QUnit.asyncTest('renders correclty', function(assert){
        QUnit.expect(30);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', gapMatchData)
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('.qti-interaction.qti-gapMatchInteraction').length, 1, 'the container contains a choice interaction .qti-gapMatchInteraction');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-prompt-container').length, 1, 'the interaction contains a prompt');
                assert.equal($container.find('.qti-gapMatchInteraction .instruction-container').length, 1, 'the interaction contains a instruction box');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area').length, 1, 'the interaction contains a choice area');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').length, 10, 'the interaction has 10 choices');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container').length, 1, 'the interaction contains a flow container');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice.qti-gap').length, 6, 'the interaction contains 6 gaps');

                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'i13806271719128107', 'the .qti-item node has the right identifier');

                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(0).data('identifier'), 'Text_1', 'the 1st choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(1).data('identifier'), 'Text_2', 'the 2nd choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(2).data('identifier'), 'Text_3', 'the 3rd choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(3).data('identifier'), 'Text_4', 'the 4th choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(4).data('identifier'), 'Text_5', 'the 5th choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(5).data('identifier'), 'Text_6', 'the 6th choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(6).data('identifier'), 'Text_7', 'the 7th choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(7).data('identifier'), 'Text_8', 'the 8th choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(8).data('identifier'), 'Text_9', 'the 9th choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(9).data('identifier'), 'Text_10', 'the 10th choice of the 1st match group has the right identifier');

                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice').eq(0).data('identifier'), 'Gap_6', 'the 1st choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice').eq(1).data('identifier'), 'Gap_1', 'the 2nd choice of the 2nd match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice').eq(2).data('identifier'), 'Gap_2', 'the 3rd choice of the 3rd match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice').eq(3).data('identifier'), 'Gap_3', 'the 4th choice of the 3rd match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice').eq(4).data('identifier'), 'Gap_4', 'the 5th choice of the 3rd match group has the right identifier');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-flow-container .qti-choice').eq(5).data('identifier'), 'Gap_5', 'the 6th choice of the 3rd match group has the right identifier');

                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to activate a choice', function(assert){
        QUnit.expect(10);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', gapMatchData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-gapMatchInteraction').length, 1, 'the container contains a choice interaction .qti-gapMatchInteraction');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-choice').length, 16, 'the interaction has 16 choices including gaps');

                var $at = $('.qti-choice[data-identifier="Text_1"]', $container);
                assert.equal($at.length, 1, 'the Authoring tool choice exists');

                var $gap = $('.gapmatch-content[data-identifier="Gap_6"]', $container);
                assert.equal($gap.length, 1, 'the gap exists');

                assert.ok( ! $at.hasClass('active'), 'The choice is not active');
                assert.ok( ! $gap.hasClass('empty'), 'The gap is not highlighted');

                $at.trigger('mousedown');

                _.delay(function(){

                    assert.ok($at.hasClass('active'), 'The choice is now active');
                    assert.ok($gap.hasClass('empty'), 'The gap is now highlighted');

                    QUnit.start();
                }, 10);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to fill a gap with a choice', function(assert){
        QUnit.expect(9);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', gapMatchData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-gapMatchInteraction').length, 1, 'the container contains a choice interaction .qti-gapMatchInteraction');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-choice').length, 16, 'the interaction has 16 choices including gaps');

                var $at = $('.qti-choice[data-identifier="Text_1"]', $container);
                assert.equal($at.length, 1, 'the Authoring tool choice exists');

                var $gap = $('.gapmatch-content[data-identifier="Gap_6"]', $container);
                assert.equal($gap.length, 1, 'the gap exists');

                $at.trigger('mousedown');

                _.delay(function(){
                    $gap.trigger('mousedown');
                }, 10);
            })
            .on('statechange', function(state){
                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE, { response : { list  : { directedPair : [ ['Text_1', 'Gap_6'] ] } } }, 'The pair CR is selected');

                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('set the default response', function(assert){
        QUnit.expect(9);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', gapMatchData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-gapMatchInteraction').length, 1, 'the container contains a choice interaction .qti-gapMatchInteraction');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-choice').length, 16, 'the interaction has 16 choices including gaps');

                var $at = $('.qti-choice[data-identifier="Text_1"]', $container);
                assert.equal($at.length, 1, 'the Authoring tool choice exists');

                var $gap = $('.gapmatch-content[data-identifier="Gap_6"]', $container);
                assert.equal($gap.length, 1, 'the gap exists');

                assert.ok( ! $gap.hasClass('filled'), 'The gap is not filled');

                  this.setState({ RESPONSE : { response : { list  : { directedPair : [ ['Text_1', 'Gap_6'] ] } } } });

                _.delay(function(){
                    assert.ok($gap.hasClass('filled'), 'The gap is now filled');
                    assert.equal($gap.text(), 'authoring tool', 'The gap contains the choice text');

                    QUnit.start();
                }, 10);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('destroys', function(assert){
        QUnit.expect(5);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        qtiItemRunner('qti', gapMatchData)
            .on('render', function(){
                var self = this;

                //call destroy manually
                var interaction = this._item.getInteractions()[0];
                interaction.renderer.destroy(interaction);

                var $at = $('.qti-choice[data-identifier="Text_1"]', $container);
                assert.equal($at.length, 1, 'the Authoring tool choice exists');

                var $gap = $('.gapmatch-content[data-identifier="Gap_6"]', $container);
                assert.equal($gap.length, 1, 'the gap exists');

                $at.trigger('mousedown');

                _.delay(function(){
                    $gap.trigger('mousedown');

                    _.delay(function(){
                        assert.deepEqual(self.getState(), {'RESPONSE': { response : { list : { directedPair : [] } } } }, 'Click does not trigger response once destroyed');

                        QUnit.start();
                    }, 100);
                }, 10);

            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('resets the response', function(assert){
        QUnit.expect(8);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', gapMatchData)
            .on('render', function(){
                var self = this;

                var $at = $('.qti-choice[data-identifier="Text_1"]', $container);
                assert.equal($at.length, 1, 'the Authoring tool choice exists');

                var $gap = $('.gapmatch-content[data-identifier="Gap_6"]', $container);
                assert.equal($gap.length, 1, 'the gap exists');

                $at.trigger('mousedown');

                _.delay(function(){
                    $gap.trigger('mousedown');

                    _.delay(function(){

                        assert.ok($gap.hasClass('filled'), 'The gap is now filled');
                        assert.equal($gap.text(), 'authoring tool', 'The gap contains the choice text');

                        //call destroy manually
                        var interaction = self._item.getInteractions()[0];
                        interaction.renderer.resetResponse(interaction);

                        _.delay(function(){

                            assert.ok( ! $gap.hasClass('filled'), 'The gap is not filled anymore');
                            assert.equal($gap.text(), '', 'The gap is now empty');

                            QUnit.start();
                        }, 100);
                    }, 100);
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('restores order of shuffled choices', function(assert){
        QUnit.expect(14);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        //hack the item data to set the shuffle attr to true
        var shuffled = _.cloneDeep(gapMatchData);
        shuffled.body.elements.interaction_gapmatchinteraction_547dd4d24d2d0146858817.attributes.shuffle = true;

        runner = qtiItemRunner('qti', shuffled)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-gapMatchInteraction').length, 1, 'the container contains a choice interaction .qti-gapMatchInteraction');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-choice').length, 16, 'the interaction has 16 choices including gaps');

                this.setState({
                    RESPONSE : {
                        response : { list : { directedPair : [] }  },
                        order : ['Text_6', 'Text_4', 'Text_7', 'Text_8', 'Text_9', 'Text_1', 'Text_10', 'Text_2', 'Text_3', 'Text_5']
                    }
                });

                _.delay(function(){

                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(0).data('identifier'), 'Text_6', 'the 1st choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(1).data('identifier'), 'Text_4', 'the 2nd choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(2).data('identifier'), 'Text_7', 'the 3rd choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(3).data('identifier'), 'Text_8', 'the 4th choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(4).data('identifier'), 'Text_9', 'the 5th choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(5).data('identifier'), 'Text_1', 'the 6th choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(6).data('identifier'), 'Text_10', 'the 7th choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(7).data('identifier'), 'Text_2', 'the 8th choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(8).data('identifier'), 'Text_3', 'the 9th choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-gapMatchInteraction .choice-area .qti-choice').eq(9).data('identifier'), 'Text_5', 'the 10th choice of the 1st match group has the right identifier');

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

        qtiItemRunner('qti', gapMatchData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-gapMatchInteraction').length, 1, 'the container contains a choice interaction .qti-gapMatchInteraction');
                assert.equal($container.find('.qti-gapMatchInteraction .qti-choice').length, 16, 'the interaction has 16 choices including gaps');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

