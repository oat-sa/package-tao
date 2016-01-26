define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/characters.json',
], function($, _, qtiItemRunner, matchData){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    module('Match Interaction', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('renders correclty', function(assert){
        QUnit.expect(20);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('.qti-interaction.qti-matchInteraction').length, 1, 'the container contains a choice interaction .qti-matchInteraction');
                assert.equal($container.find('.qti-matchInteraction .qti-prompt-container').length, 1, 'the interaction contains a prompt');
                assert.equal($container.find('.qti-matchInteraction .instruction-container').length, 1, 'the interaction contains a instruction box');
                assert.equal($container.find('.qti-matchInteraction .match-interaction-area').length, 1, 'the interaction contains an interaction area');
                assert.equal($container.find('.qti-matchInteraction .match-interaction-area table').length, 1, 'the interaction contains a table element');
                assert.equal($container.find('.qti-matchInteraction .qti-choice').length, 7, 'the interaction has 7 choices');

                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'match', 'the .qti-item node has the right identifier');

                assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(0).data('identifier'), 'C', 'the 1st choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(1).data('identifier'), 'D', 'the 2nd choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(2).data('identifier'), 'L', 'the 3rd choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(3).data('identifier'), 'P', 'the 4th choice of the 1st match group has the right identifier');

                assert.equal($container.find('.qti-matchInteraction tbody .qti-choice').eq(0).data('identifier'), 'R', 'the 1st choice of the 1st match group has the right identifier');
                assert.equal($container.find('.qti-matchInteraction tbody .qti-choice').eq(1).data('identifier'), 'M', 'the 2nd choice of the 2nd match group has the right identifier');
                assert.equal($container.find('.qti-matchInteraction tbody .qti-choice').eq(2).data('identifier'), 'T', 'the 3rd choice of the 3rd match group has the right identifier');

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

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-matchInteraction').length, 1, 'the container contains a choice interaction .qti-matchInteraction');
                assert.equal($container.find('.qti-matchInteraction .qti-choice').length, 7, 'the interaction has 5 choices');

                var $cr = $('tbody tr:eq(0) td:eq(0) input', $container);
                assert.equal($cr.length, 1, 'the CR pair exists');

                $cr.prop('checked', true).trigger('click');

            })
            .on('statechange', function(state){
                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE, { response : { list  : { directedPair : [ ['C', 'R'] ] } } }, 'The pair CR is selected');

                QUnit.start();
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('enables to select multiple choices', function(assert){
        QUnit.expect(9);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-matchInteraction').length, 1, 'the container contains a choice interaction .qti-matchInteraction');
                assert.equal($container.find('.qti-matchInteraction .qti-choice').length, 7, 'the interaction has 5 choices');

                var $cr = $('tbody tr:eq(0) td:eq(0) input', $container);
                assert.equal($cr.length, 1, 'the CR pair exists');

                var $dt = $('tbody tr:eq(2) td:eq(1) input', $container);
                assert.equal($dt.length, 1, 'the DT pair exists');

                $cr.prop('checked', true);
                $dt.prop('checked', true).trigger('click');
            })
            .on('statechange', function(state){
                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE, { response : { list  : { directedPair : [ ['C', 'R'], ['D', 'T'] ] } } }, 'The pair CR is selected');
                QUnit.start();
            })
            .init()
            .render($container);
    });


    QUnit.asyncTest('set the default response', function(assert){
        QUnit.expect(8);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){

                var $cr = $('tbody tr:eq(0) td:eq(0) input', $container);
                assert.equal($cr.length, 1, 'the CR pair exists');

                var $dt = $('tbody tr:eq(2) td:eq(1) input', $container);
                assert.equal($dt.length, 1, 'the DT pair exists');

                assert.ok( ! $cr.prop('checked'), 'The CR pair is not checked');
                assert.ok( ! $dt.prop('checked'), 'The DT pair is not checked');

                this.setState({ RESPONSE : { response : { list  : { directedPair : [ ['C', 'R'], ['D', 'T'] ] } } } });

                _.delay(function(){
                    assert.ok($cr.prop('checked'), 'The CR pair is now checked');
                    assert.ok($dt.prop('checked'), 'The DT pair is now checked');

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

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){
                var self = this;

                //call destroy manually
                var interaction = this._item.getInteractions()[0];
                interaction.renderer.destroy(interaction);

                var $cr = $('tbody tr:eq(0) td:eq(0) input', $container);
                assert.equal($cr.length, 1, 'the CR pair exists');

                $cr.prop('checked', true).trigger('click');


                _.delay(function(){

                    assert.deepEqual(self.getState(), {'RESPONSE': { response : { list : { directedPair : [] } } } }, 'Click does not trigger response once destroyed');

                    QUnit.start();
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('resets the response', function(assert){
        QUnit.expect(6);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){
                var self = this;

                var $cr = $('tbody tr:eq(0) td:eq(0) input', $container);
                assert.equal($cr.length, 1, 'the CR pair exists');

                var $dt = $('tbody tr:eq(2) td:eq(1) input', $container);
                assert.equal($dt.length, 1, 'the DT pair exists');

                $cr.prop('checked', true);
                $dt.prop('checked', true).trigger('click');

                _.delay(function(){

                    //call destroy manually
                    var interaction = self._item.getInteractions()[0];
                    interaction.renderer.resetResponse(interaction);

                    _.delay(function(){

                        assert.ok( ! $cr.prop('checked'), 'The CR pair is not checked anymore');
                        assert.ok( ! $dt.prop('checked'), 'The DT pair is not checked anymore');

                        QUnit.start();
                    }, 100);
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('restores order of shuffled choices', function(assert){
        QUnit.expect(11);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        //hack the item data to set the shuffle attr to true
        var shuffled = _.cloneDeep(matchData);
        shuffled.body.elements.interaction_matchinteraction_547481b197d23287450469.attributes.shuffle = true;

        runner = qtiItemRunner('qti', shuffled)
            .on('render', function(){
                var self = this;

                assert.equal($container.find('.qti-interaction.qti-matchInteraction').length, 1, 'the container contains a choice interaction .qti-matchInteraction');
                assert.equal($container.find('.qti-matchInteraction .qti-choice').length, 7, 'the interaction has 7 choices');

                this.setState({
                    RESPONSE : {
                        response : { base : null },
                        order : [
                            ['P', 'L', 'C', 'D'],
                            ['M', 'T', 'R']
                        ]
                    }
                });

                _.delay(function(){

                    assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(0).data('identifier'), 'P', 'the 1st choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(1).data('identifier'), 'L', 'the 2nd choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(2).data('identifier'), 'C', 'the 3rd choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-matchInteraction thead .qti-choice').eq(3).data('identifier'), 'D', 'the 4th choice of the 1st match group has the right identifier');

                    assert.equal($container.find('.qti-matchInteraction tbody .qti-choice').eq(0).data('identifier'), 'M', 'the 1st choice of the 1st match group has the right identifier');
                    assert.equal($container.find('.qti-matchInteraction tbody .qti-choice').eq(1).data('identifier'), 'T', 'the 2nd choice of the 2nd match group has the right identifier');
                    assert.equal($container.find('.qti-matchInteraction tbody .qti-choice').eq(2).data('identifier'), 'R', 'the 3rd choice of the 3rd match group has the right identifier');

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

        runner = qtiItemRunner('qti', matchData)
            .on('render', function(){

                assert.equal($container.find('.qti-interaction.qti-matchInteraction').length, 1, 'the container contains a choice interaction .qti-matchInteraction');
                assert.equal($container.find('.qti-matchInteraction .qti-choice').length, 7, 'the interaction has 5 choices');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

