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
    'ui/datalist',
    'css!taoCss/tao-3.css',
    'css!taoCss/tao-main-style.css'
], function($, _, datalist) {
    'use strict';

    // toggle the sample display
    var showSample = false;

    // display a sample of the component
    if (showSample) {
        datalist({
            renderTo: $('body'),
            selectable: true,
            tools: [{
                id: 'always',
                label: 'Always displayed',
                action: function() {
                    console.log('tool', arguments)
                }
            }, {
                id: 'selection',
                label: 'On selection',
                massAction: true,
                action: function() {
                    console.log('tool', arguments)
                }
            }],
            actions: [{
                id: 'action',
                label: 'Action',
                hidden: function() {
                    return this.id === '2';
                },
                action: function() {
                    console.log('action', arguments)
                }
            }]
        }, [{
            id: '1',
            label: 'Line 1'
        }, {
            id: '2',
            label: 'Line 2'
        }, {
            id: '3',
            label: 'Line 3'
        }]).on('select', function(selection) {
            console.log('selection', selection);
        });
    }


    QUnit.module('datalist');


    QUnit.test('module', 3, function(assert) {
        assert.equal(typeof datalist, 'function', "The datalist module exposes a function");
        assert.equal(typeof datalist(), 'object', "The datalist factory produces an object");
        assert.notStrictEqual(datalist(), datalist(), "The datalist factory provides a different object on each call");
    });


    var datalistApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'render', title : 'render' },
        { name : 'update', title : 'update' },
        { name : 'show', title : 'show' },
        { name : 'hide', title : 'hide' },
        { name : 'enable', title : 'enable' },
        { name : 'disable', title : 'disable' },
        { name : 'is', title : 'is' },
        { name : 'setState', title : 'setState' },
        { name : 'setLoading', title : 'setLoading' },
        { name : 'getSelection', title : 'getSelection' },
        { name : 'setSelection', title : 'setSelection' },
        { name : 'setTitle', title : 'setTitle' },
        { name : 'setTextNumber', title : 'setTextNumber' },
        { name : 'setTextEmpty', title : 'setTextEmpty' },
        { name : 'setTextLoading', title : 'setTextLoading' },
        { name : 'getContainer', title : 'getContainer' },
        { name : 'getElement', title : 'getElement' },
        { name : 'getTemplate', title : 'getTemplate' },
        { name : 'setTemplate', title : 'setTemplate' }
    ];

    QUnit
        .cases(datalistApi)
        .test('instance API ', function(data, assert) {
            var instance = datalist();
            assert.equal(typeof instance[data.name], 'function', 'The datalist instance exposes a "' + data.title + '" function');
        });


    QUnit.test('init', function(assert) {
        var config = {
            nothing: undefined,
            dummy: null,
            keyName : 'key',
            labelName : 'name',
            labelText : 'A label',
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait',
            selectable : true
        };
        var instance = datalist(config);

        assert.notEqual(instance.config, config, 'The datalist instance must duplicate the config set');
        assert.equal(instance.hasOwnProperty('nothing'), false, 'The datalist instance must not accept undefined config properties');
        assert.equal(instance.hasOwnProperty('dummy'), false, 'The datalist instance must not accept null config properties');
        assert.equal(instance.config.keyName, config.keyName, 'The datalist instance must catch the keyName config');
        assert.equal(instance.config.labelName, config.labelName, 'The datalist instance must catch the labelName config');
        assert.equal(instance.config.labelText, config.labelText, 'The datalist instance must catch the labelText config');
        assert.equal(instance.config.title, config.title, 'The datalist instance must catch the title config');
        assert.equal(instance.config.textNumber, config.textNumber, 'The datalist instance must catch the textNumber config');
        assert.equal(instance.config.textEmpty, config.textEmpty, 'The datalist instance must catch the textNumber config');
        assert.equal(instance.config.textLoading, config.textLoading, 'The datalist instance must catch the textNumber config');
        assert.equal(instance.config.selectable, config.selectable, 'The datalist instance must catch the selectable config');
        assert.equal(instance.is('rendered'), false, 'The datalist instance must not be rendered');

        instance.destroy();
    });


    var datalistConfigs = [{
        title: 'simple',
        config: {
            keyName : 'key',
            labelName : 'name',
            labelText : 'A label',
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait'
        }
    }, {
        title: 'selectable',
        config: {
            keyName : 'key',
            labelName : 'name',
            labelText : 'A label',
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait',
            selectable: true
        }
    }, {
        title: 'tools',
        config: {
            keyName : 'key',
            labelName : 'name',
            labelText : 'A label',
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait',
            selectable: true,
            tools: [{
                id: 'all',
                label: 'Always displayed',
                icon: 'reset'
            }, {
                id: 'select',
                label: 'Displayed if selection',
                massAction: true
            }]
        }
    }, {
        title: 'actions',
        config: {
            keyName : 'key',
            labelName : 'name',
            labelText : 'A label',
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait',
            actions: [{
                id: 'action1',
                label: 'Action 1',
                icon: 'play'
            }, {
                id: 'action2',
                label: 'Action 2',
                hidden: function() {
                    return this.id === '2';
                }
            }]
        }
    }];

    QUnit
        .cases(datalistConfigs)
        .test('render ', function(data, assert) {
            var $dummy = $('<div class="dummy" />');
            var $container = $('#fixture-1').append($dummy);
            var datalistData = [{
                key: '1',
                name: 'Line 1'
            }, {
                key: '2',
                name: 'Line 2'
            }, {
                key: '3',
                name: 'Line 3'
            }];
            var config = _.merge({
                renderTo: $container,
                replace: true
            }, data.config);
            var instance;

            // check place before render
            assert.equal($container.children().length, 1, 'The container already contains an element');
            assert.equal($container.children().get(0), $dummy.get(0), 'The container contains the dummy element');
            assert.equal($container.find('.dummy').length, 1, 'The container contains an element of the class dummy');

            // create an instance with auto rendering
            instance = datalist(config, datalistData);

            // check the rendered header
            assert.equal($container.find('.dummy').length, 0, 'The container does not contain an element of the class dummy');
            assert.equal(instance.is('rendered'), true, 'The datalist instance must be rendered');
            assert.equal(typeof instance.getElement(), 'object', 'The datalist instance returns the rendered content as an object');
            assert.equal(instance.getElement().length, 1, 'The datalist instance returns the rendered content');
            assert.equal(instance.getElement().parent().get(0), $container.get(0), 'The datalist instance is rendered inside the right container');

            assert.equal(instance.getElement().find('h1').text(), config.title, 'The datalist instance has rendered a title with the right content');
            assert.equal(instance.getElement().find('.empty-list').text(), config.textEmpty, 'The datalist instance has rendered a message to display when the list is empty, and set the right content');
            assert.equal(instance.getElement().find('.available-list .label').text(), config.textNumber, 'The datalist instance has rendered a message to show the number of boxes, and set the right content');
            assert.equal(instance.getElement().find('.available-list .count').text(), datalistData.length, 'The datalist instance displays the right number of lines');
            assert.equal(instance.getElement().find('.loading').text(), config.textLoading + '...', 'The datalist instance has rendered a message to show when the component is in loading state, and set the right content');

            // if tools are set in the config, check the action bar rendering
            if (config.tools) {
                assert.equal(instance.getElement().find('.list .action-bar').length, 1, 'The datalist instance has rendered an action bar');

                assert.equal(instance.getElement().find('.list .action-bar button').length, config.tools.length, 'The datalist instance has rendered buttons in the action bar');
                _.forEach(config.tools, function(tool) {
                    assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').length, 1, 'The datalist instance has rendered the tool button ' + tool.id);
                    assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').text().trim(), tool.label, 'The datalist instance has rendered the tool button ' + tool.id + ' with label ' + tool.label);

                    if (tool.icon) {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"] .icon').length, 1, 'The datalist instance has rendered the tool button ' + tool.id + ' with an icon');
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"] .icon').hasClass('icon-' + tool.icon), true, 'The datalist instance has rendered the tool button ' + tool.id + ' with the icon ' + tool.icon);
                    } else {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"] .icon').length, 0, 'The datalist instance has rendered the tool button ' + tool.id + ' without an icon');
                    }

                    if (tool.massAction) {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').hasClass('mass-action'), true, 'The datalist instance has rendered the tool button ' + tool.id + ' with the class mass-action');
                    } else {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').hasClass('mass-action'), false, 'The datalist instance has rendered the tool button ' + tool.id + ' without the class mass-action');
                    }
                });
            } else {
                assert.equal(instance.getElement().find('.list .action-bar').length, 0, 'The datalist instance must not render an action bar');
            }

            // check the rendered table
            assert.equal(instance.getElement().find('.list table').length, 1, 'The datalist instance has rendered a table');
            assert.equal(instance.getElement().find('.list th.label').length, 1, 'The datalist instance has rendered a label header');
            assert.equal(instance.getElement().find('.list th.label').text().trim(), config.labelText, 'The datalist instance has rendered the right text in the label header');
            if (config.selectable) {
                assert.equal(instance.getElement().find('.list th.checkboxes input').length, 1, 'The datalist instance has rendered a checkbox header');
            } else {
                assert.equal(instance.getElement().find('.list th.checkboxes input').length, 0, 'The datalist instance must not render a checkbox header');
            }

            assert.equal(instance.getElement().find('.list tbody tr').length, datalistData.length, 'The datalist instance has rendered the right number of lines');

            // check the rendered lines
            _.forEach(datalistData, function(line) {
                var id = line[config.keyName];
                var label = line[config.labelName];
                var $line = instance.getElement().find('.list tbody tr[data-id="' + id + '"]');

                assert.equal($line.length, 1, 'The datalist instance has rendered the line with id ' + id);
                assert.equal($line.find('td.label').text().trim(), label, 'The datalist instance has rendered the label ' + label + ' in the line with id ' + id);

                if (config.selectable) {
                    assert.equal($line.find('td.checkboxes input').length, 1, 'The datalist instance has rendered a checkbox in the line with id ' + id);
                    assert.equal(!$line.find('td.checkboxes input').attr('checked'), true, 'The datalist instance has rendered an unchecked checkbox in the line with id ' + id);
                } else {
                    assert.equal($line.find('td.checkboxes').length, 0, 'The datalist instance must not render a checkbox in the line with id ' + id);
                }

                if (config.actions) {
                    assert.equal($line.find('td.actions').length, 1, 'The datalist instance has rendered an actions column in the line with id ' + id);

                    _.forEach(config.actions, function(action) {
                        if (!action.hidden || !action.hidden.call(line)) {
                            assert.equal($line.find('td.actions [data-control="' + action.id + '"]').length, 1, 'The datalist instance has rendered the action button ' + action.id + ' in the line with id ' + id);
                            assert.equal($line.find('td.actions [data-control="' + action.id + '"]').text().trim(), action.label, 'The datalist instance has rendered the action button ' + action.id + ' with the label ' + action.label + ' in the line with id ' + id);

                            if (action.icon) {
                                assert.equal($line.find('td.actions [data-control="' + action.id + '"] .icon').length, 1, 'The datalist instance has rendered the action button ' + action.id + ' with an icon in the line with id ' + id);
                                assert.equal($line.find('td.actions [data-control="' + action.id + '"] .icon').hasClass('icon-' + action.icon), true, 'The datalist instance has rendered the action button ' + action.id + ' with the icon ' + action.icon + ' in the line with id ' + id);
                            } else {
                                assert.equal($line.find('td.actions [data-control="' + action.id + '"] .icon').length, 0, 'The datalist instance has rendered the action button ' + action.id + ' without an icon in the line with id ' + id);
                            }
                        } else {
                            assert.equal($line.find('td.actions [data-control="' + action.id + '"]').length, 0, 'The datalist instance must not render the hidden action button ' + action.id + ' in the line with id ' + id);
                        }

                    });
                } else {
                    assert.equal($line.find('td.actions').length, 0, 'The datalist instance must not render an actions column in the line with id ' + id);
                }
            });

            instance.destroy();

            assert.equal($container.children().length, 0, 'The container is now empty');
            assert.equal(instance.getElement(), null, 'The datalist instance has removed its rendered content');
        });


    QUnit
        .cases(datalistConfigs)
        .test('update ', function(data, assert) {
            var $dummy = $('<div class="dummy" />');
            var $container = $('#fixture-1').append($dummy);
            var datalistData = [{
                key: '1',
                name: 'Line 1'
            }, {
                key: '2',
                name: 'Line 2'
            }, {
                key: '3',
                name: 'Line 3'
            }];
            var config = _.merge({
                renderTo: $container,
                replace: true
            }, data.config);
            var instance;

            // check place before render
            assert.equal($container.children().length, 1, 'The container already contains an element');
            assert.equal($container.children().get(0), $dummy.get(0), 'The container contains the dummy element');
            assert.equal($container.find('.dummy').length, 1, 'The container contains an element of the class dummy');

            // create an instance with auto rendering
            instance = datalist(config);

            // check the rendered header
            assert.equal($container.find('.dummy').length, 0, 'The container does not contain an element of the class dummy');
            assert.equal(instance.is('rendered'), true, 'The datalist instance must be rendered');
            assert.equal(typeof instance.getElement(), 'object', 'The datalist instance returns the rendered content as an object');
            assert.equal(instance.getElement().length, 1, 'The datalist instance returns the rendered content');
            assert.equal(instance.getElement().parent().get(0), $container.get(0), 'The datalist instance is rendered inside the right container');

            assert.equal(instance.getElement().find('h1').text(), config.title, 'The datalist instance has rendered a title with the right content');
            assert.equal(instance.getElement().find('.empty-list').text(), config.textEmpty, 'The datalist instance has rendered a message to display when the list is empty, and set the right content');
            assert.equal(instance.getElement().find('.available-list .label').text(), config.textNumber, 'The datalist instance has rendered a message to show the number of boxes, and set the right content');
            assert.equal(instance.getElement().find('.available-list .count').text(), 0, 'The datalist instance displays the right number of lines');
            assert.equal(instance.getElement().find('.loading').text(), config.textLoading + '...', 'The datalist instance has rendered a message to show when the component is in loading state, and set the right content');

            // if tools are set in the config, check the action bar rendering
            if (config.tools) {
                assert.equal(instance.getElement().find('.list .action-bar').length, 1, 'The datalist instance has rendered an action bar');

                assert.equal(instance.getElement().find('.list .action-bar button').length, config.tools.length, 'The datalist instance has rendered buttons in the action bar');
                _.forEach(config.tools, function(tool) {
                    assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').length, 1, 'The datalist instance has rendered the tool button ' + tool.id);
                    assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').text().trim(), tool.label, 'The datalist instance has rendered the tool button ' + tool.id + ' with label ' + tool.label);

                    if (tool.icon) {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"] .icon').length, 1, 'The datalist instance has rendered the tool button ' + tool.id + ' with an icon');
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"] .icon').hasClass('icon-' + tool.icon), true, 'The datalist instance has rendered the tool button ' + tool.id + ' with the icon ' + tool.icon);
                    } else {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"] .icon').length, 0, 'The datalist instance has rendered the tool button ' + tool.id + ' without an icon');
                    }

                    if (tool.massAction) {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').hasClass('mass-action'), true, 'The datalist instance has rendered the tool button ' + tool.id + ' with the class mass-action');
                    } else {
                        assert.equal(instance.getElement().find('.list .action-bar [data-control="' + tool.id + '"]').hasClass('mass-action'), false, 'The datalist instance has rendered the tool button ' + tool.id + ' without the class mass-action');
                    }
                });
            } else {
                assert.equal(instance.getElement().find('.list .action-bar').length, 0, 'The datalist instance must not render an action bar');
            }

            // check the rendered table
            assert.equal(instance.getElement().find('.list table').length, 1, 'The datalist instance has rendered a table');
            assert.equal(instance.getElement().find('.list th.label').length, 1, 'The datalist instance has rendered a label header');
            assert.equal(instance.getElement().find('.list th.label').text().trim(), config.labelText, 'The datalist instance has rendered the right text in the label header');
            if (config.selectable) {
                assert.equal(instance.getElement().find('.list th.checkboxes input').length, 1, 'The datalist instance has rendered a checkbox header');
            } else {
                assert.equal(instance.getElement().find('.list th.checkboxes input').length, 0, 'The datalist instance must not render a checkbox header');
            }

            assert.equal(instance.getElement().find('.list tbody tr').length, 0, 'The datalist instance has rendered the right number of lines');

            // update the list with a collection of lines and check the rendered table
            instance.update(datalistData);

            assert.equal(instance.getElement().find('.list tbody tr').length, datalistData.length, 'The datalist instance has rendered the right number of lines');

            // check the rendered lines
            _.forEach(datalistData, function(line) {
                var id = line[config.keyName];
                var label = line[config.labelName];
                var $line = instance.getElement().find('.list tbody tr[data-id="' + id + '"]');

                assert.equal($line.length, 1, 'The datalist instance has rendered the line with id ' + id);
                assert.equal($line.find('td.label').text().trim(), label, 'The datalist instance has rendered the label ' + label + ' in the line with id ' + id);

                if (config.selectable) {
                    assert.equal($line.find('td.checkboxes input').length, 1, 'The datalist instance has rendered a checkbox in the line with id ' + id);
                    assert.equal(!$line.find('td.checkboxes input').attr('checked'), true, 'The datalist instance has rendered an unchecked checkbox in the line with id ' + id);
                } else {
                    assert.equal($line.find('td.checkboxes').length, 0, 'The datalist instance must not render a checkbox in the line with id ' + id);
                }

                if (config.actions) {
                    assert.equal($line.find('td.actions').length, 1, 'The datalist instance has rendered an actions column in the line with id ' + id);

                    _.forEach(config.actions, function(action) {
                        if (!action.hidden || !action.hidden.call(line)) {
                            assert.equal($line.find('td.actions [data-control="' + action.id + '"]').length, 1, 'The datalist instance has rendered the action button ' + action.id + ' in the line with id ' + id);
                            assert.equal($line.find('td.actions [data-control="' + action.id + '"]').text().trim(), action.label, 'The datalist instance has rendered the action button ' + action.id + ' with the label ' + action.label + ' in the line with id ' + id);

                            if (action.icon) {
                                assert.equal($line.find('td.actions [data-control="' + action.id + '"] .icon').length, 1, 'The datalist instance has rendered the action button ' + action.id + ' with an icon in the line with id ' + id);
                                assert.equal($line.find('td.actions [data-control="' + action.id + '"] .icon').hasClass('icon-' + action.icon), true, 'The datalist instance has rendered the action button ' + action.id + ' with the icon ' + action.icon + ' in the line with id ' + id);
                            } else {
                                assert.equal($line.find('td.actions [data-control="' + action.id + '"] .icon').length, 0, 'The datalist instance has rendered the action button ' + action.id + ' without an icon in the line with id ' + id);
                            }
                        } else {
                            assert.equal($line.find('td.actions [data-control="' + action.id + '"]').length, 0, 'The datalist instance must not render the hidden action button ' + action.id + ' in the line with id ' + id);
                        }
                    });
                } else {
                    assert.equal($line.find('td.actions').length, 0, 'The datalist instance must not render an actions column in the line with id ' + id);
                }
            });

            // update the list with an empty collection and check the rendered table
            instance.update([]);

            assert.equal(instance.getElement().find('.list tbody tr').length, 0, 'The datalist instance has rendered the right number of lines');

            // update the list with no collection and check the rendered table
            instance.update();

            assert.equal(instance.getElement().find('.list tbody tr').length, 0, 'The datalist instance has rendered the right number of lines');

            instance.destroy();

            assert.equal($container.children().length, 0, 'The container is now empty');
            assert.equal(instance.getElement(), null, 'The datalist instance has removed its rendered content');
        });


    QUnit.test('show/hide', function(assert) {
        var instance = datalist().render();

        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The datalist instance must be rendered');
        assert.equal($component.length, 1, 'The datalist instance returns the rendered content');

        assert.equal(instance.is('hidden'), false, 'The datalist instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The datalist instance does not have the hidden class');

        instance.hide();

        assert.equal(instance.is('hidden'), true, 'The datalist instance is hidden');
        assert.equal(instance.getElement().hasClass('hidden'), true, 'The datalist instance has the hidden class');

        instance.show();

        assert.equal(instance.is('hidden'), false, 'The datalist instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The datalist instance does not have the hidden class');

        instance.destroy();
    });


    QUnit.test('enable/disable', function(assert) {
        var instance = datalist().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The datalist instance must be rendered');
        assert.equal($component.length, 1, 'The datalist instance returns the rendered content');

        assert.equal(instance.is('disabled'), false, 'The datalist instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The datalist instance does not have the disabled class');

        instance.disable();

        assert.equal(instance.is('disabled'), true, 'The datalist instance is disabled');
        assert.equal(instance.getElement().hasClass('disabled'), true, 'The datalist instance has the disabled class');

        instance.enable();

        assert.equal(instance.is('disabled'), false, 'The datalist instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The datalist instance does not have the disabled class');

        instance.destroy();
    });


    QUnit.test('state', function(assert) {
        var instance = datalist().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The datalist instance must be rendered');
        assert.equal($component.length, 1, 'The datalist instance returns the rendered content');

        assert.equal(instance.is('customState'), false, 'The datalist instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The datalist instance does not have the customState class');

        instance.setState('customState', true);

        assert.equal(instance.is('customState'), true, 'The datalist instance has the customState state');
        assert.equal(instance.getElement().hasClass('customState'), true, 'The datalist instance has the customState class');

        instance.setState('customState', false);

        assert.equal(instance.is('customState'), false, 'The datalist instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The datalist instance does not have the customState class');

        instance.destroy();
    });


    QUnit.asyncTest('events', function(assert) {
        var config = {
            selectable: true,
            tools: [{
                id: 'tool1',
                label: 'Tool 1'
            }],
            actions: [{
                id: 'action1',
                label: 'Action 1'
            }]
        };
        var data = [{
            id: '1',
            label: 'Line 1'
        }, {
            id: '2',
            label: 'Line 2'
        }, {
            id: '3',
            label: 'Line 3'
        }];
        var instance = datalist(config);

        instance.on('custom', function() {
            assert.ok(true, 'The datalist instance can handle custom events');
            QUnit.start();
        });

        instance.on('render', function() {
            assert.ok(true, 'The datalist instance triggers event when it is rendered');
            QUnit.start();
        });

        instance.on('update', function(d) {
            assert.ok(true, 'The datalist instance triggers event when it is updated');
            assert.equal(d, data, 'The datalist instance provides the dataset when the update event is triggered');
            QUnit.start();

            instance.getElement().find('[data-control="tool1"]').click();
        });

        instance.on('tool', function(selection, buttonId) {
            assert.ok(true, 'The datalist instance triggers event when a tool button is clicked');
            assert.ok(_.isArray(selection), 'The datalist instance provides the current selection when a tool button is clicked');
            assert.equal(buttonId, 'tool1', 'The datalist instance provides the button identifier when a tool button is clicked');
            QUnit.start();

            instance.getElement().find('[data-id="1"] [data-control="action1"]').click();
        });

        instance.on('action', function(lineId, buttonId) {
            assert.ok(true, 'The datalist instance triggers event when an action button is clicked');
            assert.equal(lineId, '1', 'The datalist instance provides the line identifier when an action button is clicked');
            assert.equal(buttonId, 'action1', 'The datalist instance provides the button identifier when an action button is clicked');
            QUnit.start();

            instance.getElement().find('[data-id="1"] td.checkboxes input').on('click', function() {
                // force the check attribute as the soft click does not do this
                $(this).attr('checked', 'checked');
            }).click();
        });

        instance.on('select', function(selection) {
            assert.ok(true, 'The datalist instance triggers event when a selection is made');
            assert.ok(_.isArray(selection), 'The datalist instance provides the current selection when the select event is triggered');
            assert.equal(selection.length, 1, 'The datalist instance provides the right selection when the select event is triggered');
            assert.equal(selection[0], '1', 'The datalist instance provides the right lines identifiers when the select event is triggered');
            QUnit.start();
        });

        instance.on('destroy', function() {
            assert.ok(true, 'The datalist instance triggers event when it is destroyed');
            QUnit.start();
        });

        QUnit.expect(15);
        QUnit.stop(6);

        instance
            .render()
            .update(data)
            .trigger('custom')
            .destroy();
    });


    QUnit.asyncTest('selection', function(assert) {
        var config = {
            selectable: true,
            tools: [{
                id: 'tool1',
                label: 'Tool 1'
            }, {
                id: 'tool2',
                label: 'Tool 2',
                massAction: true
            }]
        };
        var data = [{
            id: '1',
            label: 'Line 1'
        }, {
            id: '2',
            label: 'Line 2'
        }, {
            id: '3',
            label: 'Line 3'
        }];
        var selection = [2];
        var instance = datalist(config);

        instance.on('render', function() {
            assert.equal(instance.getElement().find('[data-control="tool1"]').hasClass('mass-action'), false, 'The tool1 button is not a mass action');
            assert.equal(instance.getElement().find('[data-control="tool1"]').hasClass('hidden'), false, 'The tool1 button is visible');
            assert.equal(instance.getElement().find('[data-control="tool2"]').hasClass('mass-action'), true, 'The tool2 button is a mass action');
            assert.equal(instance.getElement().find('[data-control="tool2"]').hasClass('hidden'), true, 'The tool2 button is hidden');

            instance.update(data);
        });

        instance.on('select', function(sel) {
            assert.equal(instance.getElement().find('[data-control="tool2"]').hasClass('hidden'), !(sel && sel.length), 'The tool2 button must be visible if a selection is made');
        });

        instance.on('update', function() {
            // check pre-selection
            assert.equal(instance.getElement().find('tbody tr').length, data.length, 'The datalist instance has rendered the exact number of lines');
            assert.equal(instance.getElement().find('tbody .checkboxes').length, data.length, 'The datalist instance has rendered the checkboxes');

            assert.equal(instance.getElement().find('tbody input:checked').length, selection.length, 'The datalist instance has made a selection');

            instance.getElement().find('tbody input:checked').each(function() {
                var id = $(this).closest('tr').data('id');
                assert.ok(_.indexOf(selection, id) >= 0, 'The datalist instance has selected the right lines');
            });

            // change selection
            selection = [1, 3];
            instance.setSelection(selection);

            assert.equal(instance.getElement().find('tbody input:checked').length, selection.length, 'The datalist instance has updated the selection');

            instance.getElement().find('tbody input:checked').each(function() {
                var id = $(this).closest('tr').data('id');
                assert.ok(_.indexOf(selection, id) >= 0, 'The datalist instance has selected the right lines');
            });

            // empty selection
            instance.setSelection();

            assert.equal(instance.getElement().find('tbody input:checked').length, 0, 'The datalist instance has cleared the selection');

            instance.destroy();
        });

        instance.on('destroy', function() {
            assert.ok(true, 'The datalist instance triggers event when it is destroyed');
            QUnit.start();
        });

        instance.setSelection(selection);
        instance.render();

    });

});
