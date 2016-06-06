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
    'taoQtiTest/testRunner/actionBar/button'
], function($, _, button) {
    'use strict';

    var buttons = [{
        title: 'full button',
        id: 'full',
        testContext: {},
        testRunner: {},
        config: {
            label: 'My button',
            title: 'A test button',
            icon: 'test',
            order: 1
        }
    }, {
        title: 'button with a single label',
        id: 'btn',
        testContext: {},
        testRunner: {},
        config: {
            label: 'A button'
        }
    }, {
        title: 'empty button',
        id: 'empty',
        testContext: {},
        testRunner: {},
        config: {}
    }, {
        title: 'button with menu (inferred type)',
        id: 'menu',
        testContext: {},
        testRunner: {},
        config: {
            label: 'My menu',
            title: 'A test menu',
            icon: 'menu',
            order: 2,
            items: [{
                id: 'entry-1',
                label: 'Item 1',
                title: 'Menu entry #1',
                icon: 'style'
            }, {
                id: 'entry-2',
                label: 'Item 2',
                title: 'Menu entry #2',
                icon: 'add'
            }, {
                id: 'entry-3',
                label: 'Item 3',
                title: 'Menu entry #3',
                icon: 'remove'
            }]
        }
    }, {
        title: 'full button (specified type)',
        id: 'full',
        testContext: {},
        testRunner: {},
        config: {
            type: 'button',
            label: 'My button',
            title: 'A test button',
            icon: 'test',
            order: 1
        }
    }, {
        title: 'button with menu (specified type)',
        id: 'menu',
        testContext: {},
        testRunner: {},
        config: {
            type: 'menu',
            label: 'My menu',
            title: 'A test menu',
            icon: 'menu',
            order: 2,
            items: [{
                id: 'entry-1',
                label: 'Item 1',
                title: 'Menu entry #1',
                icon: 'style'
            }, {
                id: 'entry-2',
                label: 'Item 2',
                title: 'Menu entry #2',
                icon: 'add'
            }, {
                id: 'entry-3',
                label: 'Item 3',
                title: 'Menu entry #3',
                icon: 'remove'
            }]
        }
    }, {
        title: 'group of buttons',
        id: 'group',
        testContext: {},
        testRunner: {},
        config: {
            type: 'group',
            label: 'Unused label',
            title: 'Unused title',
            icon: 'unused icon',
            order: 2,
            items: [{
                id: 'entry-1',
                label: 'Item 1',
                title: 'Menu entry #1',
                icon: 'style'
            }, {
                id: 'entry-2',
                label: 'Item 2',
                title: 'Menu entry #2',
                icon: 'add'
            }, {
                id: 'entry-3',
                label: 'Item 3',
                title: 'Menu entry #3',
                icon: 'remove'
            }]
        }
    }];


    QUnit.module('button');


    QUnit.test('module', 7, function(assert) {
        assert.equal(typeof button, 'function', "The button module exposes a function");
        assert.equal(typeof button(), 'object', "The button factory produces an object");
        assert.notStrictEqual(button(), button(), "The button factory provides a different object on each call");
        assert.strictEqual(button().init, button().init, "The button factory provides a different object on each call, but the same methods");

        var customInit = function() {};
        var customFn = function() {};
        var btn = button({
            init : customInit,
            myCustomFn : customFn
        });
        assert.notStrictEqual(btn.init, button().init, "The button factory enable to overwrite methods and properties");
        assert.strictEqual(btn.init, customInit, "The button factory enable to overwrite methods and properties");
        assert.strictEqual(btn.myCustomFn, customFn, "The button factory enable to set custom methods and properties");
    });


    var buttonApi = [
        { name : 'init', title : 'init' },
        { name : 'clear', title : 'clear' },
        { name : 'render', title : 'render' },
        { name : 'bindTo', title : 'bindTo' },
        { name : 'bindEvents', title : 'bindEvents' },
        { name : 'unbindEvents', title : 'unbindEvents' },
        { name : 'is', title : 'is' },
        { name : 'getId', title : 'getId' },
        { name : 'getLabel', title : 'getLabel' },
        { name : 'isVisible', title : 'isVisible' },
        { name : 'hasMenu', title : 'hasMenu' },
        { name : 'isMenuOpen', title : 'isMenuOpen' },
        { name : 'closeMenu', title : 'closeMenu' },
        { name : 'openMenu', title : 'openMenu' },
        { name : 'toggleMenu', title : 'toggleMenu' },
        { name : 'setActive', title : 'setActive' },
        { name : 'trigger', title : 'trigger' },
        { name : 'on', title : 'on' },
        { name : 'off', title : 'off' },
        { name : 'setup', title : 'setup' },
        { name : 'action', title : 'action' },
        { name : 'menuAction', title : 'menuAction' },
        { name : 'enable', title : 'enable' },
        { name : 'disable', title : 'disable' },
        { name : 'show', title : 'show' },
        { name : 'hide', title : 'hide' }
    ];

    QUnit
        .cases(buttonApi)
        .test('instance API ', function(data, assert) {
            var instance = button();
            assert.equal(typeof instance[data.name], 'function', 'The button instance exposes a "' + data.title + '" function');
        });


    QUnit
        .cases(buttons)
        .test('install', function(data, assert) {
            var instance = button();
            var $button;
            var ret;

            assert.equal(typeof instance, 'object', 'A button is an object');

            ret = instance.init(data.id, data.config, data.testContext, data.testRunner);

            assert.strictEqual(ret, instance, 'The init method must return the instance');

            $button = instance.render();

            assert.equal(typeof $button, 'object', 'The rendered content is an object');
            assert.ok(!!$button.jquery, 'The rendered content is a jQuery selection');
            assert.equal($button.length, 1, 'The rendered content is one DOM element');

            if (instance.is('button')) {
                assert.equal($button.data('control'), data.id, 'The button must have the right identifier');
                assert.ok($button.hasClass('action-button'), 'The button must be a class of action-button');
            }

            if (data.config.label !== undefined) {
                if (instance.is('button')) {
                    assert.equal($button.find('>a .label').text(), data.config.label, 'The label must be displayed');

                    if (!data.config.title) {
                        assert.equal($button.attr('title'), data.config.label, 'The title must be equal to the label');
                    }
                } else {
                    assert.notEqual($button.find('.label').first().text(), data.config.label, 'No label must be displayed');
                }

            }

            if (data.config.title !== undefined) {
                if (instance.is('button')) {
                    assert.equal($button.attr('title'), data.config.title, 'The title must be set with the correct value');
                } else {
                    assert.equal($button.attr('title'), undefined, 'The title must not be set');
                }
            }

            if (data.config.order !== undefined) {
                assert.equal($button.data('order'), data.config.order, 'The order data must be set with the correct value');
            }

            if (data.config.icon !== undefined && !instance.is('group')) {
                assert.ok($button.find('>a .icon').hasClass('icon-' + data.config.icon), 'The icon must be set with the correct class');
            }

            if (data.config.items !== undefined) {
                if (instance.is('menu')) {
                    assert.equal($button.find('.menu').length, 1, 'The button must contain a menu');
                    assert.equal($button.find('.menu-item').length, data.config.items.length, 'The button menu must contain the exact number of entries');

                    data.config.items.forEach(function(item) {
                        var $item = $button.find('[data-control="' + item.id + '"]');
                        assert.equal($item.length, 1, "The button menu must contain the entry " + item.id);

                        if (item.label !== undefined) {
                            assert.equal($item.find('>a .label').text(), item.label, 'The menu item label must be displayed');

                            if (!item.title) {
                                assert.equal($item.attr('title'), item.label, 'The menu item title must be equal to the label');
                            }
                        }

                        if (item.title !== undefined) {
                            assert.equal($item.attr('title'), item.title, 'The menu item title must be set with the correct value');
                        }

                        if (item.icon !== undefined) {
                            assert.ok($item.find('>a .icon').hasClass('icon-' + item.icon), 'The menu item icon must be set with the correct class');
                        }
                    });
                }

                if (instance.is('group')) {
                    assert.equal($button.find('.action-button').length, data.config.items.length, 'The buttons group must contain the exact number of entries');

                    data.config.items.forEach(function(btn) {
                        var $btn = $button.find('[data-control="' + btn.id + '"]');
                        assert.equal($btn.length, 1, "The group button must contain the entry " + btn.id);

                        if (btn.label !== undefined) {
                            assert.equal($btn.find('.label').text(), btn.label, 'The group button label must be displayed');

                            if (!btn.title) {
                                assert.equal($btn.attr('title'), btn.label, 'The group button title must be equal to the label');
                            }
                        }

                        if (btn.title !== undefined) {
                            assert.equal($btn.attr('title'), btn.title, 'The group button title must be set with the correct value');
                        }

                        if (btn.icon !== undefined) {
                            assert.ok($btn.find('.icon').hasClass('icon-' + btn.icon), 'The group button icon must be set with the correct class');
                        }
                    });
                }
            } else {
                assert.equal($button.find('.menu').length, 0, 'The button must nost contain a menu');
                assert.equal($button.find('.menu-item').length, 0, 'There is not button menu entries');
            }

            instance.clear();
    });


    QUnit.asyncTest('events [button]', function(assert) {
        var instance = button();
        var data = buttons[0];
        var $container = $('#button-1');
        var $button;

        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $container.append($button);

        instance.action = function(id, $btn) {
            assert.ok(true, 'The action method has been called');
            assert.equal(id, data.id, 'The action method provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action method provide a button element');
            assert.ok(!!$btn.jquery, 'The action method provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action method provide the right button element');
            assert.equal($btn.data('control'), data.id, 'The action method provide the right button element');
            QUnit.start();
        };

        instance.on('action', function(e, id, $btn, btn) {
            assert.ok(true, 'The action event has been triggered');
            assert.equal(id, data.id, 'The action event provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action event provide a button element');
            assert.ok(!!$btn.jquery, 'The action event provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action event provide the right button element');
            assert.equal($btn.data('control'), data.id, 'The action event provide the right button element');
            assert.strictEqual(btn, instance, 'The action event provide the right button instance');
            QUnit.start();
        });

        QUnit.expect(13);
        QUnit.stop();
        $button.click();
    });

    QUnit.asyncTest('events [menu]', function(assert) {
        var instance = button();
        var data = buttons[3];
        var expectedMenuOpenState = false;
        var expectedMenuConfig = data.config.items[0];
        var $container = $('#button-2');
        var $button;
        var $menu;
        var $menuItem;

        QUnit.expect(66);
        QUnit.stop(7);

        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $menu = $button.find('.menu');
        $container.append($button);

        assert.equal($menu.length, 1, 'The button must contain a menu');

        instance.action = function(id, $btn) {
            assert.ok(true, 'The action method has been called');
            assert.equal(id, data.id, 'The action method provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action method provide a button element');
            assert.ok(!!$btn.jquery, 'The action method provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action method provide the right button element');
            assert.equal($btn.data('control'), data.id, 'The action method provide the right button element');
            QUnit.start();
        };

        instance.menuAction = function(id, $item) {
            assert.ok(true, 'The menuAction method has been called');
            assert.equal(id, expectedMenuConfig.id, 'The menuAction method provide the right button id');
            assert.equal(typeof $item, 'object', 'The menuAction method provide a button element');
            assert.ok(!!$item.jquery, 'The menuAction method provide a button element as a jQuery selection');
            assert.ok($item.is('.menu-item'), 'The menuAction method provide the right button element');
            assert.equal($item.data('control'), expectedMenuConfig.id, 'The menuAction method provide the right button element');

            QUnit.start();
        };

        instance.on('action', function(e, id, $btn, btn) {
            assert.ok(true, 'The action event has been triggered');
            assert.equal(id, data.id, 'The action event provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action event provide a button element');
            assert.ok(!!$btn.jquery, 'The action event provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action event provide the right button element');
            assert.equal($btn.data('control'), data.id, 'The action event provide the right button element');
            assert.strictEqual(btn, instance, 'The action event provide the right button instance');

            expectedMenuOpenState = !expectedMenuOpenState;
            assert.equal(instance.isMenuOpen(), expectedMenuOpenState, 'The menu must be ' + (expectedMenuOpenState ? 'open' : 'closed'));
            assert.equal($menu.hasClass('hidden'), !expectedMenuOpenState, 'The menu state must be ' + (expectedMenuOpenState ? 'open' : 'closed'));
            assert.equal($button.hasClass('active'), expectedMenuOpenState, 'The button state must be ' + (expectedMenuOpenState ? 'active' : 'inactive'));

            QUnit.start();
        });

        instance.on('menuaction', function(e, id, $item, btn) {
            assert.ok(true, 'The menuaction event has been triggered');
            assert.equal(id, expectedMenuConfig.id, 'The menuaction event provide the right button id');
            assert.equal(typeof $item, 'object', 'The menuaction event provide a button element');
            assert.ok(!!$item.jquery, 'The menuaction event provide a button element as a jQuery selection');
            assert.ok($item.is('.menu-item'), 'The menuaction event provide the right button element');
            assert.equal($item.data('control'), expectedMenuConfig.id, 'The menuaction event provide the right button element');

            expectedMenuOpenState = !expectedMenuOpenState;
            assert.equal(instance.isMenuOpen(), expectedMenuOpenState, 'The menu must be ' + (expectedMenuOpenState ? 'open' : 'closed'));
            assert.equal($menu.hasClass('hidden'), !expectedMenuOpenState, 'The menu state must be ' + (expectedMenuOpenState ? 'open' : 'closed'));
            assert.equal($button.hasClass('active'), expectedMenuOpenState, 'The button state must be ' + (expectedMenuOpenState ? 'active' : 'inactive'));

            QUnit.start();
        });

        assert.equal(instance.isMenuOpen(), expectedMenuOpenState, 'The menu must be closed');

        $button.click();
        $button.click();
        $button.click();
        $menuItem = $menu.find('.action').first();
        $menuItem.click();

        assert.equal(instance.getActiveMenu(), expectedMenuConfig.id, 'The selected menu must the right one');
    });


    QUnit.asyncTest('events [group]', function(assert) {
        var instance = button();
        var data = buttons[6];
        var expectedButtonConfig = data.config.items[1];
        var $container = $('#button-3');
        var $button;

        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $container.append($button);

        instance.action = function(id, $btn) {
            assert.ok(true, 'The action method has been called');
            assert.equal(id, expectedButtonConfig.id, 'The action method provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action method provide a button element');
            assert.ok(!!$btn.jquery, 'The action method provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action method provide the right button element');
            assert.equal($btn.data('control'), expectedButtonConfig.id, 'The action method provide the right button element');
            QUnit.start();
        };

        instance.on('action', function(e, id, $btn, btn) {
            assert.ok(true, 'The action event has been triggered');
            assert.equal(id, expectedButtonConfig.id, 'The action event provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action event provide a button element');
            assert.ok(!!$btn.jquery, 'The action event provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action event provide the right button element');
            assert.equal($btn.data('control'), expectedButtonConfig.id, 'The action event provide the right button element');
            assert.strictEqual(btn, instance, 'The action event provide the right button instance');
            QUnit.start();
        });

        QUnit.expect(13);
        QUnit.stop();
        $button.children().eq(1).click();
    });


    QUnit.test('menu selection', function(assert) {
        var instance = button();
        var data = buttons[3];
        var expectedMenuConfig = data.config.items[0];
        var expectedMenuId = 'entry-3';
        var $container = $('#button-4');
        var $button;
        var $menu;
        var $menuItem;


        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $menu = $button.find('.menu');
        $container.append($button);

        assert.equal($menu.length, 1, 'The button must contain a menu');

        $menuItem = $menu.find('.action').first();
        $menuItem.click();

        assert.equal(instance.getActiveMenu(), expectedMenuConfig.id, 'The selected menu must the right one');

        instance.setActiveMenu(expectedMenuId);

        assert.equal(instance.getActiveMenu(), expectedMenuId, 'The new selected menu must the right one');

        instance.clearActiveMenu();

        assert.strictEqual(instance.getActiveMenu(), null, 'The menu selection must cleared');
    });


    QUnit.test('group selection', function(assert) {
        var instance = button();
        var data = buttons[6];
        var expectedMenuConfig = data.config.items[1];
        var $container = $('#button-5');
        var $button;
        var $btn;

        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $container.append($button);

        assert.equal($button.find('.action-button').length, data.config.items.length, 'The buttons group must contain the exact number of entries');

        $btn = $button.find('.action-button').eq(1);

        assert.ok(!$btn.hasClass('active'), 'The button must not be selected');
        assert.equal($button.find('.active').length, 0, 'The number of active buttons must be 0');

        instance.setActive(true, expectedMenuConfig.id);

        assert.ok($btn.hasClass('active'), 'The button must be selected');
        assert.equal($button.find('.active').length, 1, 'The number of active buttons must be 1');

        instance.setActive(false, expectedMenuConfig.id);

        assert.ok(!$btn.hasClass('active'), 'The button must not be selected');
        assert.equal($button.find('.active').length, 0, 'The number of active buttons must be 0');

    });


    QUnit.asyncTest('enable/disable', function(assert) {
        var instance = button();
        var data = buttons[0];
        var $container = $('#button-6');
        var $button;
        var actionEnabled = true;
        var callCount = 0;

        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $container.append($button);

        instance.action = function(id, $btn) {
            callCount ++;

            assert.equal(true, actionEnabled, 'The action method has been called');
            assert.equal(id, data.id, 'The action method provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action method provide a button element');
            assert.ok(!!$btn.jquery, 'The action method provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action method provide the right button element');
            assert.equal($btn.data('control'), data.id, 'The action method provide the right button element');
            QUnit.start();
        };

        instance.on('action', function(e, id, $btn, btn) {
            callCount ++;

            assert.equal(true, actionEnabled, 'The action event has been triggered');
            assert.equal(id, data.id, 'The action event provide the right button id');
            assert.equal(typeof $btn, 'object', 'The action event provide a button element');
            assert.ok(!!$btn.jquery, 'The action event provide a button element as a jQuery selection');
            assert.ok($btn.is('.action-button'), 'The action event provide the right button element');
            assert.equal($btn.data('control'), data.id, 'The action event provide the right button element');
            assert.strictEqual(btn, instance, 'The action event provide the right button instance');
            QUnit.start();
        });

        QUnit.expect(32);
        QUnit.stop(3);

        assert.ok(!$button.hasClass('disabled'), 'This button is enabled');

        $button.click();

        assert.equal(callCount, 2, 'Action can be called when button is enabled');

        instance.disable();
        actionEnabled = false;

        assert.ok($button.hasClass('disabled'), 'This button is disabled');

        $button.click();

        assert.equal(callCount, 2, 'No action called when button is disabled');

        instance.enable();
        actionEnabled = true;

        assert.ok(!$button.hasClass('disabled'), 'This button is enabled');

        $button.click();

        assert.equal(callCount, 4, 'Action can be called when button is enabled');
    });

    QUnit.asyncTest('show/hide', 4, function(assert) {
        var instance = button();
        var data = buttons[0];
        var $container = $('#button-7');
        var $button;

        instance.init(data.id, data.config, data.testContext, data.testRunner);

        $button = instance.render();
        $container.append($button);

        assert.equal($button.length, 1, 'the button exists');
        assert.notEqual($button.css('display'), 'none', 'the button is displayed');

        instance.hide();

        _.defer(function(){
            assert.equal($button.css('display'), 'none', 'the button is hidden');

            instance.show();

            _.defer(function(){
                assert.notEqual($button.css('display'), 'none', 'the button is back');

                QUnit.start();
            });
        });
    });


});
