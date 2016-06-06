define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/media/qti.json'
], function($, _, qtiItemRunner, mediaData){
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    var sampleUrl = '../../../taoQtiItem/views/js/test/samples/json/media/sample.mp4';

    QUnit.module('Media Interaction', {
        teardown : function(){
            if(runner){
                runner.clear();
            }
        }
    });

    QUnit.asyncTest('renders correctly', function(assert){
        QUnit.expect(12);

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', mediaData)
            .on('error', function(e){

                assert.ok(false, e);
                QUnit.start();
            })
            .on('render', function(){

                //check DOM
                assert.equal($container.children().length, 1, 'the container a elements');
                assert.equal($container.children('.qti-item').length, 1, 'the container contains a the root element .qti-item');
                assert.equal($container.find('.qti-itemBody').length, 1, 'the container contains a the body element .qti-itemBody');
                assert.equal($container.find('.qti-interaction').length, 1, 'the container contains an interaction .qti-interaction');
                assert.equal($container.find('.qti-interaction.qti-mediaInteraction').length, 1, 'the container contains a choice interaction .qti-mediaInteraction');
                assert.equal($container.find('.qti-mediaInteraction .qti-prompt-container').length, 1, 'the interaction contains a prompt');
                assert.equal($container.find('.qti-mediaInteraction .instruction-container').length, 1, 'the interaction contains a instruction box');
                assert.equal($container.find('.qti-mediaInteraction video').length, 1, 'the interaction contains a video tag');
                assert.equal($container.find('.qti-mediaInteraction video').attr('src'), sampleUrl, 'the interaction has proper file attached');

                //check DOM data
                assert.equal($container.children('.qti-item').data('identifier'), 'i1429259831305858', 'the .qti-item node has the right identifier');

                QUnit.start();
            })
            .assets(function(url){
                if(/\.mp4$/.test(url.toString())){
                    return sampleUrl;
                }
                return url.toString();
            })
            .init()
            .render($container);
    });


    QUnit.module('Visual Test');

    QUnit.asyncTest('Display and play', function(assert){
        QUnit.expect(4);

        var $container = $('#' + outsideContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', mediaData)
            .on('render', function(){
                assert.equal($container.find('.qti-interaction.qti-mediaInteraction').length, 1, 'the container contains a choice interaction .qti-mediaInteraction');
                assert.equal($container.find('.qti-mediaInteraction video').length, 1, 'the interaction has element');

                QUnit.start();
            })
            .assets(function(url){
                if(/\.mp4$/.test(url.toString())){
                    return sampleUrl;
                }
                return url.toString();
            })
            .init()
            .render($container);
    });
});

