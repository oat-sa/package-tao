define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/space-shuttle.json',
    'json!taoQtiItem/test/samples/json/space-shuttle-m.json',
    'core/promise'
], function($, _, qtiItemRunner, choiceData, multipleChoiceData, Promise){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    module('Choice Interaction', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('renders correclty', function(assert){
        QUnit.expect(17);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', choiceData)
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-prompt-container').length, 1, 'the interaction contains a prompt');
                assert.equal($container.find('.qti-choiceInteraction .instruction-container').length, 1, 'the interaction contains a instruction box');
                assert.equal($container.find('.qti-choiceInteraction .choice-area').length, 1, 'the interaction contains a choice list');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'space-shuttle-30-years-of-adventure', 'the .qti-item node has the right identifier');

                assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(1)').data('identifier'), 'Discovery', 'the 1st choice has the right identifier');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(2)').data('identifier'), 'Challenger', 'the 2nd choice has the right identifier');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(3)').data('identifier'), 'Pathfinder', 'the 3rd choice has the right identifier');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(4)').data('identifier'), 'Atlantis', 'the 4th choice has the right identifier');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(5)').data('identifier'), 'Endeavour', 'the 5th choice has the right identifier');

                QUnit.start();
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('enables to select a choice', function(assert){
        QUnit.expect(8);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', choiceData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                var $discovery = $('.qti-choice[data-identifier="Discovery"]', $container);
                assert.equal($discovery.length, 1, 'the Discovery choice exists');

                $discovery.trigger('click');

            })
            .on('statechange', function(state){
                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE, { response : { base  : { identifier : 'Discovery' } } }, 'The discovery response is selected');
                QUnit.start();
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('enables to select a unique choice', function(assert){
        QUnit.expect(11);

        var $container = $('#' + fixtureContainerId);
        var changes = 0;

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', choiceData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                var $discovery = $('.qti-choice[data-identifier="Discovery"]', $container);
                assert.equal($discovery.length, 1, 'the Discovery choice exists');

                var $challenger = $('.qti-choice[data-identifier="Challenger"]', $container);
                assert.equal($discovery.length, 1, 'the Challenger choice exists');

                $discovery.trigger('click');
                _.delay(function(){
                    $challenger.trigger('click');
                }, 200);

            })
            .on('statechange', function(state){
                if(++changes === 2){
                    //check the response is challenger
                    assert.ok(typeof state === 'object', 'The state is an object');
                    assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                    assert.deepEqual(state.RESPONSE, { response : { base  : { identifier : 'Challenger' } } }, 'The Challenger response is selected');

                    //Challenger is checked instead of Discovery
                    assert.ok( ! $('[data-identifier="Discovery"] input', $container).prop('checked'), 'Discovery is not checked');
                    assert.ok($('[data-identifier="Challenger"] input', $container).prop('checked'), 'Challenger is now checked');

                    QUnit.start();
                }
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('enables to select multiple choices', function(assert){
        QUnit.expect(11);

        var $container = $('#' + fixtureContainerId);
        var changes = 0;

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', multipleChoiceData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                assert.equal($container.find('.qti-choiceInteraction .instruction-container').length, 1, 'the interaction contains an instruction box');
                assert.equal($container.find('.qti-choiceInteraction .instruction-container').children().length, 2, 'the interaction has 2 instructions');

                var $discovery = $('.qti-choice[data-identifier="Discovery"]', $container);
                assert.equal($discovery.length, 1, 'the Discovery choice exists');

                var $challenger = $('.qti-choice[data-identifier="Challenger"]', $container);
                assert.equal($discovery.length, 1, 'the Challenger choice exists');

                $discovery.trigger('click');
                _.delay(function(){
                    $challenger.trigger('click');
                }, 200);
            })
            .on('statechange', function(state){
                if(++changes === 2){
                    assert.ok(typeof state === 'object', 'The state is an object');
                    assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                    assert.deepEqual(state.RESPONSE, { response : { list  : { identifier : ['Discovery', 'Challenger'] } } }, 'Discovery AND Challenger are selected');
                    QUnit.start();
                }
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('set the default response', function(assert){
        QUnit.expect(4);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', choiceData)
            .on('render', function(){

                assert.ok( ! $('[data-identifier="Atlantis"] input', $container).prop('checked'), 'Atlantis is not checked');

                this.setState({ RESPONSE : { response : {  base : { identifier : 'Atlantis' } } } });

                assert.ok($('[data-identifier="Atlantis"] input', $container).prop('checked'), 'Atlantis is now checked');

                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('destroys', function(assert){
        QUnit.expect(5);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', choiceData)
            .on('render', function(){
                var self = this;

                //call destroy manually
                var interaction = this._item.getInteractions()[0];
                interaction.renderer.destroy(interaction);

                var $discovery = $('.qti-choice[data-identifier="Discovery"]', $container);
                assert.equal($discovery.length, 1, 'the Discovery choice exists');

                $discovery.trigger('click');

                _.delay(function(){

                    assert.deepEqual(self.getState(), {'RESPONSE': { response : { base : null } } }, 'Click does not trigger response once destroyed');
                    assert.equal($container.find('.qti-choiceInteraction .instruction-container').children().length, 0, 'there is no instructions anymore');

                    QUnit.start();
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('resets the response', function(assert){
        QUnit.expect(7);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', choiceData)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                var $discovery = $('.qti-choice[data-identifier="Discovery"]', $container);
                assert.equal($discovery.length, 1, 'the Discovery choice exists');

                $discovery.trigger('click');

                _.delay(function(){
                    assert.ok($('input', $discovery).prop('checked'), 'Discovery is now checked');

                    //call destroy manually
                    var interaction = self._item.getInteractions()[0];
                    interaction.renderer.resetResponse(interaction);

                    _.delay(function(){

                        assert.ok( ! $('input', $discovery).prop('checked'), 'Discovery is not checked checked anymore');

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
        var shuffled = _.cloneDeep(choiceData);
        shuffled.body.elements.interaction_choiceinteraction_546cb89e04090230494786.attributes.shuffle = true;

        runner = qtiItemRunner('qti', shuffled)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                this.setState({
                    RESPONSE : {
                        response : { base : null },
                        order : ['Challenger', 'Atlantis', 'Pathfinder', 'Discovery', 'Endeavour']
                    }
                });

                _.delay(function(){

                    assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(1)').data('identifier'), 'Challenger', 'the 1st choice has the right identifier');
                    assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(2)').data('identifier'), 'Atlantis', 'the 2nd choice has the right identifier');
                    assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(3)').data('identifier'), 'Pathfinder', 'the 3rd choice has the right identifier');
                    assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(4)').data('identifier'), 'Discovery', 'the 4th choice has the right identifier');
                    assert.equal($container.find('.qti-choiceInteraction .qti-choice:nth-child(5)').data('identifier'), 'Endeavour', 'the 5th choice has the right identifier');

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

        qtiItemRunner('qti', choiceData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-choiceInteraction').length, 1, 'the container contains a choice interaction .qti-choiceInteraction');
                assert.equal($container.find('.qti-choiceInteraction .qti-choice').length, 5, 'the interaction has 5 choices');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

