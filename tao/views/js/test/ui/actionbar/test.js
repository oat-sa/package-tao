/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */

/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'ui/actionbar',
    'css!taoCss/tao-3.css',
    'css!taoCss/tao-main-style.css'
], function ($, _, actionbar) {
    'use strict';

    // toggle the sample display
    var showSample = false;

    // display a sample of the component
    if (showSample) {
        actionbar({
            renderTo: $('body'),
            buttons: [{
                id: 'btn1',
                label: 'Button 1',
                action: function () {
                    console.log('button 1', arguments)
                }
            }, {
                id: 'btn2',
                label: 'Button 2',
                action: function () {
                    console.log('button 2', arguments)
                }
            }, {
                id: 'btnx',
                label: 'Button ...',
                action: function () {
                    console.log('button ...', arguments)
                }
            }, {
                id: 'btnN',
                label: 'Button N',
                action: function () {
                    console.log('button n', arguments)
                }
            }]
        });
    }


    QUnit.module('actionbar');


    QUnit.test('module', 3, function (assert) {
        assert.equal(typeof actionbar, 'function', "The actionbar module exposes a function");
        assert.equal(typeof actionbar(), 'object', "The actionbar factory produces an object");
        assert.notStrictEqual(actionbar(), actionbar(), "The actionbar factory provides a different object on each call");
    });


    var datalistApi = [
        {name: 'init', title: 'init'},
        {name: 'destroy', title: 'destroy'},
        {name: 'render', title: 'render'},
        {name: 'show', title: 'show'},
        {name: 'hide', title: 'hide'},
        {name: 'enable', title: 'enable'},
        {name: 'disable', title: 'disable'},
        {name: 'is', title: 'is'},
        {name: 'setState', title: 'setState'},
        {name: 'getButton', title: 'getButton'},
        {name: 'getButtonElement', title: 'getButtonElement'},
        {name: 'showButton', title: 'showButton'},
        {name: 'hideButton', title: 'hideButton'},
        {name: 'toggleButton', title: 'toggleButton'},
        {name: 'showConditionals', title: 'showConditionals'},
        {name: 'hideConditionals', title: 'hideConditionals'},
        {name: 'toggleConditionals', title: 'toggleConditionals'},
        {name: 'showAll', title: 'showAll'},
        {name: 'hideAll', title: 'hideAll'},
        {name: 'toggleAll', title: 'toggleAll'},
        {name: 'getContainer', title: 'getContainer'},
        {name: 'getElement', title: 'getElement'},
        {name: 'getTemplate', title: 'getTemplate'},
        {name: 'setTemplate', title: 'setTemplate'}
    ];

    QUnit
        .cases(datalistApi)
        .test('instance API ', function (data, assert) {
            var instance = actionbar();
            assert.equal(typeof instance[data.name], 'function', 'The actionbar instance exposes a "' + data.title + '" function');
        });


    QUnit.test('init', function (assert) {
        var buttons = [];
        var config = {
            nothing: undefined,
            dummy: null,
            buttons: buttons
        };
        var instance = actionbar(config);

        assert.notEqual(instance.config, config, 'The actionbar instance must duplicate the config set');
        assert.equal(instance.hasOwnProperty('nothing'), false, 'The actionbar instance must not accept undefined config properties');
        assert.equal(instance.hasOwnProperty('dummy'), false, 'The actionbar instance must not accept null config properties');
        assert.equal(instance.config.buttons, config.buttons, 'The actionbar instance must catch the buttons config');
        assert.equal(instance.is('rendered'), false, 'The actionbar instance must not be rendered');

        instance.destroy();
    });


    QUnit.test('render ', function (assert) {
        var $dummy = $('<div class="dummy" />');
        var $container = $('#fixture-1').append($dummy);
        var config = {
            renderTo: $container,
            replace: true,
            buttons: [{
                id: 'btn1',
                label: 'Button 1'
            }, {
                id: 'btn2',
                label: 'Button 2'
            }]
        };
        var instance;

        // check place before render
        assert.equal($container.children().length, 1, 'The container already contains an element');
        assert.equal($container.children().get(0), $dummy.get(0), 'The container contains the dummy element');
        assert.equal($container.find('.dummy').length, 1, 'The container contains an element of the class dummy');

        // create an instance with auto rendering
        instance = actionbar(config);

        // check the rendered header
        assert.equal($container.find('.dummy').length, 0, 'The container does not contain an element of the class dummy');
        assert.equal(instance.is('rendered'), true, 'The actionbar instance must be rendered');
        assert.equal(typeof instance.getElement(), 'object', 'The actionbar instance returns the rendered content as an object');
        assert.equal(instance.getElement().length, 1, 'The actionbar instance returns the rendered content');
        assert.equal(instance.getElement().parent().get(0), $container.get(0), 'The actionbar instance is rendered inside the right container');

        assert.equal(instance.is('horizontal'), true, 'The actionbar instance is horizontal');
        assert.equal(instance.getElement().hasClass('horizontal-action-bar'), true, 'The actionbar instance is rendered horizontally');


        assert.equal(instance.getElement().find('button').length, config.buttons.length, 'The actionbar instance has rendered the buttons');
        _.forEach(config.buttons, function (button) {
            assert.equal(instance.getElement().find('[data-control="' + button.id + '"]').length, 1, 'The actionbar instance has rendered the button ' + button.id);
            assert.equal(instance.getElement().find('[data-control="' + button.id + '"]').text().trim(), button.label, 'The actionbar instance has rendered the button ' + button.id + ' with label ' + button.label);

            if (button.icon) {
                assert.equal(instance.getElement().find('[data-control="' + button.id + '"] .icon').length, 1, 'The actionbar instance has rendered the button ' + button.id + ' with an icon');
                assert.equal(instance.getElement().find('[data-control="' + button.id + '"] .icon').hasClass('icon-' + button.icon), true, 'The actionbar instance has rendered the button ' + button.id + ' with the icon ' + button.icon);
            } else {
                assert.equal(instance.getElement().find('[data-control="' + button.id + '"] .icon').length, 0, 'The actionbar instance has rendered the button ' + button.id + ' without an icon');
            }

            if (button.conditional) {
                assert.equal(instance.getElement().find('[data-control="' + button.id + '"]').hasClass('conditional'), true, 'The actionbar instance has rendered the button ' + button.id + ' with the class conditional');
            } else {
                assert.equal(instance.getElement().find('[data-control="' + button.id + '"]').hasClass('conditional'), false, 'The actionbar instance has rendered the button ' + button.id + ' without the class conditional');
            }
        });

        instance.destroy();

        config.vertical = true;
        instance = actionbar(config);
        assert.equal(instance.is('vertical'), true, 'The actionbar instance is vertical');
        assert.equal(instance.getElement().hasClass('vertical-action-bar'), true, 'The actionbar instance is rendered vertically');
        instance.destroy();

        assert.equal($container.children().length, 0, 'The container is now empty');
        assert.equal(instance.getElement(), null, 'The actionbar instance has removed its rendered content');
    });


    QUnit.test('show/hide', function (assert) {
        var instance = actionbar().render();

        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The actionbar instance must be rendered');
        assert.equal($component.length, 1, 'The actionbar instance returns the rendered content');

        assert.equal(instance.is('hidden'), false, 'The actionbar instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The actionbar instance does not have the hidden class');

        instance.hide();

        assert.equal(instance.is('hidden'), true, 'The actionbar instance is hidden');
        assert.equal(instance.getElement().hasClass('hidden'), true, 'The actionbar instance has the hidden class');

        instance.show();

        assert.equal(instance.is('hidden'), false, 'The actionbar instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The actionbar instance does not have the hidden class');

        instance.destroy();
    });


    QUnit.test('show/hide buttons', function (assert) {
        var config = {
            buttons: [{
                id: 'b1',
                conditional: true
            }, {
                id: 'b2'
            }]
        };
        var instance = actionbar(config).render();

        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The actionbar instance must be rendered');
        assert.equal($component.length, 1, 'The actionbar instance returns the rendered content');
        assert.equal($component.find('button').length, config.buttons.length, 'The actionbar instance has rendered the buttons');

        assert.equal($component.find('button.conditional').hasClass('hidden'), true, 'The conditional buttons are hidden by default');
        assert.equal($component.find('button:not(.conditional)').hasClass('hidden'), false, 'The normal buttons are visible by default');

        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), true, 'The button b1 is hidden');
        instance.showButton('b1');
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');

        assert.equal(instance.getButtonElement('b2').hasClass('hidden'), false, 'The button b2 is visible');
        instance.hideButton('b2');
        assert.equal(instance.getButtonElement('b2').hasClass('hidden'), true, 'The button b2 is hidden');

        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');
        instance.hideConditionals();
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), true, 'The button b1 is hidden');
        instance.showConditionals();
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');

        instance.showAll();
        assert.equal($component.find('button').hasClass('hidden'), false, 'The buttons are all visible');
        instance.hideAll();
        assert.equal($component.find('button').hasClass('hidden'), true, 'The buttons are all hidden');

        instance.destroy();
    });


    QUnit.test('toggle buttons', function (assert) {
        var config = {
            buttons: [{
                id: 'b1',
                conditional: true
            }, {
                id: 'b2'
            }]
        };
        var instance = actionbar(config).render();

        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The actionbar instance must be rendered');
        assert.equal($component.length, 1, 'The actionbar instance returns the rendered content');
        assert.equal($component.find('button').length, config.buttons.length, 'The actionbar instance has rendered the buttons');

        assert.equal($component.find('button.conditional').hasClass('hidden'), true, 'The conditional buttons are hidden by default');
        assert.equal($component.find('button:not(.conditional)').hasClass('hidden'), false, 'The normal buttons are visible by default');

        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), true, 'The button b1 is hidden');
        instance.toggleButton('b1', true);
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');
        instance.toggleButton('b1');
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), true, 'The button b1 is hidden');
        instance.toggleButton('b1');
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');

        assert.equal(instance.getButtonElement('b2').hasClass('hidden'), false, 'The button b2 is visible');
        instance.toggleButton('b2', false);
        assert.equal(instance.getButtonElement('b2').hasClass('hidden'), true, 'The button b2 is hidden');

        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');
        instance.toggleConditionals(false);
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), true, 'The button b1 is hidden');
        instance.toggleConditionals(true);
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');
        instance.toggleConditionals();
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), true, 'The button b1 is hidden');
        instance.toggleConditionals();
        assert.equal(instance.getButtonElement('b1').hasClass('hidden'), false, 'The button b1 is visible');

        instance.toggleAll(true);
        assert.equal($component.find('button').hasClass('hidden'), false, 'The buttons are all visible');
        instance.toggleAll(false);
        assert.equal($component.find('button').hasClass('hidden'), true, 'The buttons are all hidden');
        instance.toggleAll();
        assert.equal($component.find('button').hasClass('hidden'), false, 'The buttons are all visible');
        instance.toggleAll();
        assert.equal($component.find('button').hasClass('hidden'), true, 'The buttons are all hidden');

        instance.destroy();
    });


    QUnit.test('enable/disable', function (assert) {
        var instance = actionbar().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The actionbar instance must be rendered');
        assert.equal($component.length, 1, 'The actionbar instance returns the rendered content');

        assert.equal(instance.is('disabled'), false, 'The actionbar instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The actionbar instance does not have the disabled class');

        instance.disable();

        assert.equal(instance.is('disabled'), true, 'The actionbar instance is disabled');
        assert.equal(instance.getElement().hasClass('disabled'), true, 'The actionbar instance has the disabled class');

        instance.enable();

        assert.equal(instance.is('disabled'), false, 'The actionbar instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The actionbar instance does not have the disabled class');

        instance.destroy();
    });


    QUnit.test('state', function (assert) {
        var instance = actionbar().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The actionbar instance must be rendered');
        assert.equal($component.length, 1, 'The actionbar instance returns the rendered content');

        assert.equal(instance.is('customState'), false, 'The actionbar instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The actionbar instance does not have the customState class');

        instance.setState('customState', true);

        assert.equal(instance.is('customState'), true, 'The actionbar instance has the customState state');
        assert.equal(instance.getElement().hasClass('customState'), true, 'The actionbar instance has the customState class');

        instance.setState('customState', false);

        assert.equal(instance.is('customState'), false, 'The actionbar instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The actionbar instance does not have the customState class');

        instance.destroy();
    });


    QUnit.asyncTest('events', function (assert) {
        var config = {
            selectable: true,
            buttons: [{
                id: 'button1',
                label: 'Button 1',
                action: function(buttonId) {
                    assert.ok(true, 'The actionbar instance call the right action a button is clicked');
                    assert.equal(buttonId, 'button1', 'The actionbar instance provides the button identifier when a button is clicked');
                    QUnit.start();
                }
            }]
        };
        var instance = actionbar(config);

        instance.on('custom', function () {
            assert.ok(true, 'The actionbar instance can handle custom events');
            QUnit.start();
        });

        instance.on('render', function () {
            assert.ok(true, 'The actionbar instance triggers event when it is rendered');
            QUnit.start();

            instance.getElement().find('[data-control="button1"]').click();
        });

        instance.on('button', function (buttonId) {
            assert.ok(true, 'The actionbar instance triggers event when aﬁ button is clicked');
            assert.equal(buttonId, 'button1', 'The actionbar instance provides the button identifier when a button is clicked');
            QUnit.start();
        });

        instance.on('destroy', function () {
            assert.ok(true, 'The actionbar instance triggers event when it is destroyed');
            QUnit.start();
        });

        QUnit.expect(7);
        QUnit.stop(4);

        instance
            .render()
            .trigger('custom')
            .destroy();
    });

});
