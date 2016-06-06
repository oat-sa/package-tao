define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/rivals.json',
], function($, _, qtiItemRunner, associateData){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';


    module('Associate Interaction', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('renders correclty', function(assert){
        QUnit.expect(21);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', associateData)
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains a associate interaction .qti-associateInteraction');
                assert.equal($container.find('.qti-associateInteraction .qti-prompt-container').length, 1, 'the interaction contains a prompt');
                assert.equal($container.find('.qti-associateInteraction .instruction-container').length, 1, 'the interaction contains a instruction box');
                assert.equal($container.find('.qti-associateInteraction .choice-area').length, 1, 'the interaction contains a choice list');
                assert.equal($container.find('.qti-associateInteraction .qti-choice').length, 6, 'the interaction has 6 choices');
                assert.equal($container.find('.qti-associateInteraction .result-area').length, 1, 'the interaction has a result area');

                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'associate', 'the .qti-item node has the right identifier');

                assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(1)').data('identifier'), 'A', 'the 1st choice has the right identifier');
                assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(2)').data('identifier'), 'C', 'the 2nd choice has the right identifier');
                assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(3)').data('identifier'), 'D', 'the 3rd choice has the right identifier');
                assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(4)').data('identifier'), 'L', 'the 4th choice has the right identifier');
                assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(5)').data('identifier'), 'M', 'the 5th choice has the right identifier');
                assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(6)').data('identifier'), 'P', 'the 6th choice has the right identifier');

                assert.equal($container.find('.qti-associateInteraction .result-area').children().length, 3, 'the interaction has 3 pairs area according to maxAssocation');
                assert.equal($container.find('.qti-associateInteraction .result-area .target').length, 6, 'the interaction has 6 target box according to maxAssocation (3 pairs)');

                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to activate a choice', function(assert){
        QUnit.expect(11);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', associateData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains an associate interaction .qti-associateInteraction');

                var $antonio = $('.qti-choice[data-identifier="A"]', $container);
                assert.equal($antonio.length, 1, 'the A choice exists');

                var $target = $('.result-area .target', $container).first();
                assert.equal($target.length, 1, 'the target exists');

                assert.ok( ! $antonio.hasClass('active'), 'The choice is not active');
                assert.ok( ! $target.hasClass('empty'), 'The target is not highlighted');

                $antonio.trigger('mousedown');

                _.delay(function(){

                    assert.ok( $antonio.hasClass('active'), 'The choice is active');
                    assert.ok( $target.hasClass('empty'), 'The target is highlighted');

                    _.delay(function(){
                        $antonio.trigger('mousedown');

                        assert.ok( ! $antonio.hasClass('active'), 'The choice is not active anymore');
                        assert.ok( ! $target.hasClass('empty'), 'The target is not highlighted anymore');

                        QUnit.start();
                    }, 100);
                }, 100);

            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to create a pair', function(assert){
        QUnit.expect(20);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        qtiItemRunner('qti', associateData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains an associate interaction .qti-associateInteraction');

                var $antonio = $('.qti-choice[data-identifier="A"]', $container);
                assert.equal($antonio.length, 1, 'the A choice exists');

                var $capulet = $('.qti-choice[data-identifier="C"]', $container);
                assert.equal($capulet.length, 1, 'the C choice exists');

                var $target1 = $('.result-area li:first-child .lft', $container);
                assert.equal($target1.length, 1, 'the target exists');

                var $target2 = $('.result-area li:first-child .rgt', $container);
                assert.equal($target2.length, 1, 'the target exists');

                $antonio.trigger('mousedown');

                _.delay(function(){
                    $target1.trigger('mousedown');

                    _.delay(function(){
                        $capulet.trigger('mousedown');

                        _.delay(function(){
                            $target2.trigger('mousedown');
                        }, 10);
                    }, 10);
                }, 10);

            })
            .on('statechange', function(state){

                var $antonio = $('.qti-choice[data-identifier="A"]', $container);
                assert.equal($antonio.length, 1, 'the A choice exists');
                assert.ok($antonio.hasClass('deactivated'), 'the A choice is deactivated');

                var $capulet = $('.qti-choice[data-identifier="C"]', $container);
                assert.equal($capulet.length, 1, 'the C choice exists');
                assert.ok($capulet.hasClass('deactivated'), 'the C choice is deactivated');

                var $target1 = $('.result-area li:first-child .lft', $container);
                assert.equal($target1.length, 1, 'the target exists');
                assert.ok($target1.hasClass('filled'), 'the target is filled');
                assert.equal($target1.text(), 'Antonio', 'the target contains the choice text');

                var $target2 = $('.result-area li:first-child .rgt', $container);
                assert.equal($target2.length, 1, 'the target exists');
                assert.ok($target2.hasClass('filled'), 'the target is filled');
                assert.equal($target2.text(), 'Capulet', 'the target contains the choice text');

                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE.response, { list : { pair : [ ['A', 'C'] ] } }, 'The pair is in the response');

                QUnit.start();
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('enables to use a choice multiple times', function(assert){
        QUnit.expect(14);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        var pChoiceMatchMax = associateData.body.elements.interaction_associateinteraction_54787e6dad70d437146538.choices.choice_simpleassociablechoice_54787e6dadcdd949770698.attributes.matchMax;
        assert.equal(pChoiceMatchMax, 2, "The matchMax attributes of the P choice is set at 2");

        qtiItemRunner('qti', associateData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains an associate interaction .qti-associateInteraction');

                var $prospero = $('.qti-choice[data-identifier="P"]', $container);
                assert.equal($prospero.length, 1, 'the A choice exists');
                assert.ok( ! $prospero.hasClass('deactivated'), 'the P choice is not deactivated');

                var $target1 = $('.result-area li:first-child .lft', $container);
                assert.equal($target1.length, 1, 'the target exists');

                var $target2 = $('.result-area li:first-child .rgt', $container);
                assert.equal($target2.length, 1, 'the target exists');

                $prospero.trigger('mousedown');

                _.delay(function(){
                    $target1.trigger('mousedown');

                    _.delay(function(){
                        assert.ok($target1.hasClass('filled'), 'the target is filled');
                        assert.equal($target1.text(), 'Prospero', 'the target contains the choice text');
                        assert.ok( ! $prospero.hasClass('deactivated'), 'the P choice is still not deactivated');

                        $prospero.trigger('mousedown');

                        _.delay(function(){
                            $target2.trigger('mousedown');

                            _.delay(function(){
                                assert.ok($target2.hasClass('filled'), 'the target is filled');
                                assert.equal($target2.text(), 'Prospero', 'the target contains the choice text');
                                assert.ok($prospero.hasClass('deactivated'), 'the P choice is now deactivated');

                                QUnit.start();
                            }, 10);
                        }, 10);
                    }, 10);
                }, 10);

            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('set the default response', function(assert){
        QUnit.expect(17);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        qtiItemRunner('qti', associateData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains an associate interaction .qti-associateInteraction');

                var $antonio = $('.qti-choice[data-identifier="A"]', $container);
                assert.equal($antonio.length, 1, 'the A choice exists');
                assert.ok( ! $antonio.hasClass('deactivated'), 'the A choice is not deactivated');

                var $capulet = $('.qti-choice[data-identifier="C"]', $container);
                assert.equal($capulet.length, 1, 'the C choice exists');
                assert.ok( ! $capulet.hasClass('deactivated'), 'the C choice is not deactivated');

                var $target1 = $('.result-area li:first-child .lft', $container);
                assert.equal($target1.length, 1, 'the target exists');
                assert.ok( ! $target1.hasClass('filled'), 'the target is not filled');

                var $target2 = $('.result-area li:first-child .rgt', $container);
                assert.equal($target2.length, 1, 'the target exists');
                assert.ok( ! $target2.hasClass('filled'), 'the target is not filled');

                this.setState({ RESPONSE : { response : { list : { pair : [ ['A', 'C'] ] } } } });

                _.delay(function(){

                    assert.ok($antonio.hasClass('deactivated'), 'the A choice is deactivated');
                    assert.ok($capulet.hasClass('deactivated'), 'the C choice is deactivated');
                    assert.ok($target1.hasClass('filled'), 'the target is filled');
                    assert.equal($target1.text(), 'Antonio', 'the target contains the choice text');
                    assert.ok($target2.hasClass('filled'), 'the target is filled');
                    assert.equal($target2.text(), 'Capulet', 'the target contains the choice text');

                    QUnit.start();
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('destroys', function(assert){
        QUnit.expect(4);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        qtiItemRunner('qti', associateData)
            .on('render', function(){
                var self = this;

                //call destroy manually
                var interaction = this._item.getInteractions()[0];
                interaction.renderer.destroy(interaction);

                var $antonio = $('.qti-choice[data-identifier="A"]', $container);
                assert.equal($antonio.length, 1, 'the A choice exists');

                $antonio.trigger('mousedown');

                _.delay(function(){

                    assert.deepEqual(self.getState(), {RESPONSE: { response :  {  list : { pair : [] }  } } }, 'Click does not trigger response once destroyed');

                    QUnit.start();
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('resets the response', function(assert){
        QUnit.expect(14);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        qtiItemRunner('qti', associateData)
            .on('render', function(){
                var self = this;

                var $antonio = $('.qti-choice[data-identifier="A"]', $container);
                assert.equal($antonio.length, 1, 'the A choice exists');

                var $capulet = $('.qti-choice[data-identifier="C"]', $container);
                assert.equal($capulet.length, 1, 'the C choice exists');

                var $target1 = $('.result-area li:first-child .lft', $container);
                assert.equal($target1.length, 1, 'the target exists');

                var $target2 = $('.result-area li:first-child .rgt', $container);
                assert.equal($target2.length, 1, 'the target exists');

                $antonio.trigger('mousedown');

                _.delay(function(){
                    $target1.trigger('mousedown');

                    _.delay(function(){
                        $capulet.trigger('mousedown');

                        _.delay(function(){
                            $target2.trigger('mousedown');

                            assert.ok($antonio.hasClass('deactivated'), 'the A choice is deactivated');
                            assert.ok($capulet.hasClass('deactivated'), 'the C choice is deactivated');
                            assert.ok($target1.hasClass('filled'), 'the target is filled');
                            assert.ok($target2.hasClass('filled'), 'the target is filled');

                            //call reset Response manually
                            var interaction = self._item.getInteractions()[0];
                            interaction.renderer.resetResponse(interaction);

                            _.delay(function(){

                                assert.ok( ! $antonio.hasClass('deactivated'), 'the A choice is not deactivated anymore');
                                assert.ok( ! $capulet.hasClass('deactivated'), 'the C choice is not deactivated anymore');
                                assert.ok( ! $target1.hasClass('filled'), 'the target is not filled anymore');
                                assert.ok( ! $target2.hasClass('filled'), 'the target is not filled anymore');

                                QUnit.start();
                            }, 100);

                        }, 10);
                    }, 10);
                }, 10);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('restores order of shuffled choices', function(assert){
        QUnit.expect(10);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        //hack the item data to set the shuffle attributes to true
        var shuffled = _.cloneDeep(associateData);
        shuffled.body.elements.interaction_associateinteraction_54787e6dad70d437146538.attributes.shuffle = true;

        qtiItemRunner('qti', shuffled)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains a choice interaction .qti-associateInteraction');
                assert.equal($container.find('.qti-associateInteraction .qti-choice').length, 6, 'the interaction has 6 choices');

                this.setState({
                    RESPONSE : {
                        response : { list : { pair : [] } },
                        order : ['M', 'L', 'C', 'D', 'A', 'P']
                    }
                });

                _.delay(function(){

                    assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(1)').data('identifier'), 'M', 'the 1st choice has the right identifier');
                    assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(2)').data('identifier'), 'L', 'the 2nd choice has the right identifier');
                    assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(3)').data('identifier'), 'C', 'the 3rd choice has the right identifier');
                    assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(4)').data('identifier'), 'D', 'the 4th choice has the right identifier');
                    assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(5)').data('identifier'), 'A', 'the 5th choice has the right identifier');
                    assert.equal($container.find('.qti-associateInteraction .qti-choice:nth-child(6)').data('identifier'), 'P', 'the 6th choice has the right identifier');

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

        qtiItemRunner('qti', associateData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-associateInteraction').length, 1, 'the container contains a choice interaction .qti-associateInteraction');
                assert.equal($container.find('.qti-associateInteraction .qti-choice').length, 6, 'the interaction has 6 choices');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

