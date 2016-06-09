define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/richardIII-1.json'
], function($, _, qtiItemRunner, inlineChoiceData){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    module('Inline Choice Interaction', {
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

        runner = qtiItemRunner('qti', inlineChoiceData)
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('select.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('select.qti-interaction.qti-inlineChoiceInteraction').length, 1, 'the container contains a choice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option').length, 5, 'the interaction has 5 options');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                //check select2
                assert.equal($container.find('.select2-container.qti-interaction.qti-inlineChoiceInteraction').length, 1, 'select2 is initialized');


                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'inlineChoice', 'the .qti-item node has the right identifier');

                assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(1)').val(), '', 'the 1st choice has no value');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(1)').text(), '', 'the 1st choice is empty');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(2)').val(), 'empty', 'the 2nd choice has a value of "empty"');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(3)').data('identifier'), 'G', 'the 3rd choice has the right identifier');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(4)').data('identifier'), 'L', 'the 4th choice has the right identifier');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(5)').data('identifier'), 'Y', 'the 5th choice has the right identifier');

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

        runner = qtiItemRunner('qti', inlineChoiceData)
            .on('render', function(){

                var $select = $('select.qti-inlineChoiceInteraction', $container);
                assert.equal($select.length, 1, 'the container contains an inlineChoice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                var $select2Container = $('.select2-container', $container);
                assert.equal($select2Container.length, 1, 'select2 is initialized');

                $select.select2('val', 'L').trigger('change');
            })
            .on('statechange', function(state){

                assert.ok(typeof state === 'object', 'The state is an object');
                assert.ok(typeof state.RESPONSE === 'object', 'The state has a response object');
                assert.deepEqual(state.RESPONSE, { response : { base  : { identifier : 'L' } } }, 'The lancaster response is selected');
                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('set the default response', function(assert){
        QUnit.expect(6);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', inlineChoiceData)
            .on('render', function(){

                var $select = $('select.qti-inlineChoiceInteraction', $container);
                assert.equal($select.length, 1, 'the container contains an inlineChoice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                assert.equal($select.select2('val'), '', 'There is no choice selected');

                this.setState({ RESPONSE : { response : { base  : { identifier : 'G' } } } });

                _.delay(function(){
                    assert.equal($select.select2('val'), 'G', 'The G choice is selected');

                    QUnit.start();
                }, 10);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('destroys', function(assert){
        QUnit.expect(6);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', inlineChoiceData)
            .on('render', function(){
                var self = this;

                var $select = $('select.qti-inlineChoiceInteraction', $container);
                assert.equal($select.length, 1, 'the container contains an inlineChoice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                var $select2Container = $('.select2-container', $container);
                assert.equal($select2Container.length, 1, 'select2 is initialized');

                //call destroy manually
                var interaction = this._item.getInteractions()[0];
                interaction.renderer.destroy(interaction);

                _.delay(function(){
                    $select.select2('val', 'L').trigger('change');

                    _.delay(function(){

                        assert.deepEqual(self.getState(), {'RESPONSE': { response : { base : null } } }, 'Updating the values does not trigger response once destroyed');

                        QUnit.start();
                    }, 100);
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

        runner = qtiItemRunner('qti', inlineChoiceData)
            .on('render', function(){
                var self = this;

                var $select = $('select.qti-inlineChoiceInteraction', $container);
                assert.equal($select.length, 1, 'the container contains an inlineChoice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                var $select2Container = $('.select2-container', $container);
                assert.equal($select2Container.length, 1, 'select2 is initialized');

                $select.select2('val', 'L').trigger('change');

                _.delay(function(){

                    assert.equal($select.val(), 'L', 'The value is set to Lancaster');

                    //call destroy manually
                    var interaction = self._item.getInteractions()[0];
                    interaction.renderer.resetResponse(interaction);

                    _.delay(function(){

                        assert.equal($select.val(), 'empty', 'The value is now empty');
                        QUnit.start();
                    }, 100);
                }, 100);
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('restores order of shuffled choices', function(assert){
        QUnit.expect(10);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        //hack the item data to set the shuffle attr to true
        var shuffled = _.cloneDeep(inlineChoiceData);
        shuffled.body.elements.interaction_inlinechoiceinteraction_547464dbc7afc574464937.attributes.shuffle = true;

        runner = qtiItemRunner('qti', shuffled)
            .on('render', function(){
                var self = this;

                var $select = $('select.qti-inlineChoiceInteraction', $container);
                assert.equal($select.length, 1, 'the container contains an inlineChoice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                this.setState({
                    RESPONSE : {
                        response : { base : null  },
                        order : ['Y', 'G', 'L']
                    }
                });

                _.delay(function(){

                    assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(1)').val(), '', 'the 1st choice has no value');
                    assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(1)').text(), '', 'the 1st choice is empty');
                    assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(2)').val(), 'empty', 'the 2nd choice has a value of "empty"');
                    assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(3)').data('identifier'), 'Y', 'the 3rd choice has the right identifier');
                    assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(4)').data('identifier'), 'G', 'the 4th choice has the right identifier');
                    assert.equal($container.find('select.qti-inlineChoiceInteraction option:nth-child(5)').data('identifier'), 'L', 'the 5th choice has the right identifier');

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

        runner = qtiItemRunner('qti', inlineChoiceData)
            .on('render', function(){
                var $select = $('select.qti-inlineChoiceInteraction', $container);
                assert.equal($select.length, 1, 'the container contains an inlineChoice interaction .qti-inlineChoiceInteraction');
                assert.equal($container.find('select.qti-inlineChoiceInteraction option[data-identifier]').length, 3, 'the interaction has 3 choices');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

