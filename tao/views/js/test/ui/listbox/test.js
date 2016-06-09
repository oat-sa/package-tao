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
    'ui/listbox'
], function($, _, listBox) {
    'use strict';

    QUnit.module('listBox');


    QUnit.test('module', 3, function(assert) {
        assert.equal(typeof listBox, 'function', "The listBox module exposes a function");
        assert.equal(typeof listBox(), 'object', "The listBox factory produces an object");
        assert.notStrictEqual(listBox(), listBox(), "The listBox factory provides a different object on each call");
    });


    var testReviewApi = [
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
        .cases(testReviewApi)
        .test('instance API ', function(data, assert) {
            var instance = listBox();
            assert.equal(typeof instance[data.name], 'function', 'The listBox instance exposes a "' + data.title + '" function');
            instance.destroy();
        });


    QUnit.test('init', function(assert) {
        var config = {
            nothing: undefined,
            dummy: null,
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait'
        };
        var instance = listBox(config);

        assert.notEqual(instance.config, config, 'The listBox instance must duplicate the config set');
        assert.equal(instance.hasOwnProperty('nothing'), false, 'The listBox instance must not accept undefined config properties');
        assert.equal(instance.hasOwnProperty('dummy'), false, 'The listBox instance must not accept null config properties');
        assert.equal(instance.config.title, config.title, 'The listBox instance must catch the title config');
        assert.equal(instance.config.textNumber, config.textNumber, 'The listBox instance must catch the textNumber config');
        assert.equal(instance.config.textEmpty, config.textEmpty, 'The listBox instance must catch the textNumber config');
        assert.equal(instance.config.textLoading, config.textLoading, 'The listBox instance must catch the textNumber config');
        assert.equal(instance.is('rendered'), false, 'The listBox instance must not be rendered');

        instance.destroy();
    });


    QUnit.test('render', function(assert) {
        var $dummy = $('<div class="dummy" />');
        var $container = $('#fixture-1').append($dummy);
        var config = {
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait',
            renderTo: $container,
            replace: true,
            width: 8,
            list: [{
                url: 'http://localhost/test',
                label: 'Test',
                text: 'test',
                content: '<b>TEST</b>'
            }, {
                url: 'http://localhost/test2',
                label: 'Test2',
                text: 'test2',
                content: '<b>TEST2</b>',
                width: 4,
                cls: 'myclass'
            }]
        };
        var instance;

        assert.equal($container.children().length, 1, 'The container already contains an element');
        assert.equal($container.children().get(0), $dummy.get(0), 'The container contains the dummy element');
        assert.equal($container.find('.dummy').length, 1, 'The container contains an element of the class dummy');

        instance = listBox(config);

        assert.equal($container.find('.dummy').length, 0, 'The container does not contain an element of the class dummy');
        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal(typeof instance.getElement(), 'object', 'The listBox instance returns the rendered content as an object');
        assert.equal(instance.getElement().length, 1, 'The listBox instance returns the rendered content');
        assert.equal(instance.getElement().parent().get(0), $container.get(0), 'The listBox instance is rendered inside the right container');

        assert.equal(instance.getElement().find('h1').text(), config.title, 'The listBox instance has rendered a title with the right content');
        assert.equal(instance.getElement().find('.empty-list').text(), config.textEmpty, 'The listBox instance has rendered a message to display when the list is empty, and set the right content');
        assert.equal(instance.getElement().find('.available-list .label').text(), config.textNumber, 'The listBox instance has rendered a message to show the number of boxes, and set the right content');
        assert.equal(instance.getElement().find('.available-list .count').text(), config.list.length, 'The listBox instance displays the right number of boxes');
        assert.equal(instance.getElement().find('.loading').text(), config.textLoading + '...', 'The listBox instance has rendered a message to show when the component is in loading state, and set the right content');

        assert.equal(instance.getElement().find('.list .entry').length, config.list.length, 'The listBox instance has rendered the list of boxes');

        // 1st
        assert.equal(instance.getElement().find('.list .entry').first().hasClass('flex-col-8'), true, 'The listBox instance has set the right flex width in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('a').attr('href'), config.list[0].url, 'The listBox instance has set the right url in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('h3').text(), config.list[0].label, 'The listBox instance has set the right label in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('.content').html(), config.list[0].content, 'The listBox instance has set the content text in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('.text-link').text(), config.list[0].text, 'The listBox instance has set the right bottom text in the first entry');

        // 2nd
        assert.equal(instance.getElement().find('.list .entry').last().hasClass('flex-col-4'), true, 'The listBox instance has set the right flex width in the second entry');
        assert.equal(instance.getElement().find('.list .entry').last().hasClass('myclass'), true, 'The listBox instance has set an extra CSS class in the second entry');
        assert.equal(instance.getElement().find('.list .entry').last().find('a').attr('href'), config.list[1].url, 'The listBox instance has set the right url in the second entry');
        assert.equal(instance.getElement().find('.list .entry').last().find('h3').text(), config.list[1].label, 'The listBox instance has set the right label in the second entry');
        assert.equal(instance.getElement().find('.list .entry').last().find('.content').html(), config.list[1].content, 'The listBox instance has set the content text in the second entry');
        assert.equal(instance.getElement().find('.list .entry').last().find('.text-link').text(), config.list[1].text, 'The listBox instance has set the right bottom text in the second entry');

        instance.destroy();

        assert.equal($container.children().length, 0, 'The container is now empty');
        assert.equal(instance.getElement(), null, 'The listBox instance has removed its rendered content');
    });


    QUnit.test('update', function(assert) {
        var instance = listBox().render();
        var $component = instance.getElement();
        var list = [{
            url: 'http://localhost/test',
            label: 'Test',
            text: 'test',
            content: '<b>TEST</b>'
        }];

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal($component.length, 1, 'The listBox instance returns the rendered content');

        assert.equal(instance.getElement().find('.list .entry').length, 0, 'The listBox instance has rendered an empty list');
        assert.equal(instance.getElement().hasClass('empty'), true, 'The listBox instance displays a message telling the list is empty');
        assert.equal(instance.getElement().hasClass('loaded'), false, 'The listBox instance does not display the number of boxes');
        assert.equal(instance.getElement().hasClass('loading'), false, 'The listBox instance is not loading');

        assert.equal(instance.is('empty'), true, 'The listBox instance has the state empty');
        assert.equal(instance.is('loaded'), false, 'The listBox instance does not have the state loaded');
        assert.equal(instance.is('loading'), false, 'The listBox instance does not have the state loading');

        assert.equal(instance.getElement().find('.available-list .count').text(), 0, 'The listBox instance displays the right number of boxes');

        instance.update(list);

        assert.equal(instance.getElement().find('.list .entry').length, list.length, 'The listBox instance has rendered the list of boxes');
        assert.equal(instance.getElement().find('.list .entry').first().hasClass('flex-col-12'), true, 'The listBox instance has set the right flex width in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('a').attr('href'), list[0].url, 'The listBox instance has set the right url in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('h3').text(), list[0].label, 'The listBox instance has set the right label in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('.content').html(), list[0].content, 'The listBox instance has set the content text in the first entry');
        assert.equal(instance.getElement().find('.list .entry').first().find('.text-link').text(), list[0].text, 'The listBox instance has set the right bottom text in the first entry');

        assert.equal(instance.getElement().hasClass('empty'), false, 'The listBox instance does not display a message telling the list is empty');
        assert.equal(instance.getElement().hasClass('loaded'), true, 'The listBox instance displays the number of boxes');
        assert.equal(instance.getElement().hasClass('loading'), false, 'The listBox instance is not loading');
        assert.equal(instance.getElement().find('.available-list .count').text(), list.length, 'The listBox instance displays the right number of boxes');

        assert.equal(instance.is('empty'), false, 'The listBox instance does not have the state empty');
        assert.equal(instance.is('loaded'), true, 'The listBox instance has the state loaded');
        assert.equal(instance.is('loading'), false, 'The listBox instance does not have the state loading');

        instance.destroy();
    });


    QUnit.test('show/hide', function(assert) {
        var instance = listBox().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal($component.length, 1, 'The listBox instance returns the rendered content');

        assert.equal(instance.is('hidden'), false, 'The listBox instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The listBox instance does not have the hidden class');

        instance.hide();

        assert.equal(instance.is('hidden'), true, 'The listBox instance is hidden');
        assert.equal(instance.getElement().hasClass('hidden'), true, 'The listBox instance has the hidden class');

        instance.show();

        assert.equal(instance.is('hidden'), false, 'The listBox instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The listBox instance does not have the hidden class');

        instance.destroy();
    });


    QUnit.test('enable/disable', function(assert) {
        var instance = listBox().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal($component.length, 1, 'The listBox instance returns the rendered content');

        assert.equal(instance.is('disabled'), false, 'The listBox instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The listBox instance does not have the disabled class');

        instance.disable();

        assert.equal(instance.is('disabled'), true, 'The listBox instance is disabled');
        assert.equal(instance.getElement().hasClass('disabled'), true, 'The listBox instance has the disabled class');

        instance.enable();

        assert.equal(instance.is('disabled'), false, 'The listBox instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The listBox instance does not have the disabled class');

        instance.destroy();
    });


    QUnit.test('state', function(assert) {
        var instance = listBox().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal($component.length, 1, 'The listBox instance returns the rendered content');

        // loading
        assert.equal(instance.is('loading'), false, 'The listBox instance is not loading');
        assert.equal(instance.getElement().hasClass('loading'), false, 'The listBox instance does not have the loading class');

        instance.setLoading(true);

        assert.equal(instance.is('loading'), true, 'The listBox instance is loading');
        assert.equal(instance.getElement().hasClass('loading'), true, 'The listBox instance has the loading class');

        instance.setLoading(false);

        assert.equal(instance.is('loading'), false, 'The listBox instance is not loading');
        assert.equal(instance.getElement().hasClass('loading'), false, 'The listBox instance does not have the loading class');

        // custom state
        assert.equal(instance.is('customState'), false, 'The listBox instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The listBox instance does not have the customState class');

        instance.setState('customState', true);

        assert.equal(instance.is('customState'), true, 'The listBox instance has the customState state');
        assert.equal(instance.getElement().hasClass('customState'), true, 'The listBox instance has the customState class');

        instance.setState('customState', false);

        assert.equal(instance.is('customState'), false, 'The listBox instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The listBox instance does not have the customState class');

        instance.destroy();
    });


    QUnit.test('setters', function(assert) {
        var config = {
            title: 'My Title',
            textEmpty: 'Nothing to list',
            textNumber: 'Number',
            textLoading: 'Please wait'
        };
        var instance = listBox().render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal($component.length, 1, 'The listBox instance returns the rendered content');

        assert.notEqual(instance.config.title, config.title, 'The listBox instance has its own title');
        assert.notEqual(instance.config.textEmpty, config.textEmpty, 'The listBox instance has its own empty list message');
        assert.notEqual(instance.config.textNumber, config.textNumber, 'The listBox instance has its own number label');
        assert.notEqual(instance.config.textLoading, config.textLoading, 'The listBox instance has its own loading message');

        assert.notEqual(instance.getElement().find('h1').text(), config.title, 'The listBox instance has rendered a title with its own content');
        assert.notEqual(instance.getElement().find('.empty-list').text(), config.textEmpty, 'The listBox instance has rendered a message to display when the list is empty, and set its own content');
        assert.notEqual(instance.getElement().find('.available-list .label').text(), config.textNumber, 'The listBox instance has rendered a message to show the number of boxes, and set its own content');
        assert.notEqual(instance.getElement().find('.loading').text(), config.textLoading + '...', 'The listBox instance has rendered a message to show when the component is in loading state, and set its own content');

        instance.setTitle(config.title);
        assert.equal(instance.config.title, config.title, 'The listBox instance has taken the right title');
        assert.equal(instance.getElement().find('h1').text(), config.title, 'The listBox instance has updated the title with the right content');

        instance.setTextEmpty(config.textEmpty);
        assert.equal(instance.config.textEmpty, config.textEmpty, 'The listBox instance has the right empty list message');
        assert.equal(instance.getElement().find('.empty-list').text(), config.textEmpty, 'The listBox instance has updated the message to display when the list is empty, and set the right content');

        instance.setTextNumber(config.textNumber);
        assert.equal(instance.config.textNumber, config.textNumber, 'The listBox instance has the right number label');
        assert.equal(instance.getElement().find('.available-list .label').text(), config.textNumber, 'The listBox instance has updated the number label, and set the right content');

        instance.setTextLoading(config.textLoading);
        assert.equal(instance.config.textLoading, config.textLoading, 'The listBox instance has the right loading label');
        assert.equal(instance.getElement().find('.loading').text(), config.textLoading + '...', 'The listBox instance has updated the loading label, and set the right content');

        instance.destroy();

        assert.equal(instance.is('rendered'), false, 'The listBox instance must be destroyed');

        instance.render();
        $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal($component.length, 1, 'The listBox instance returns the rendered content');

        assert.equal(instance.config.title, config.title, 'The listBox instance has its own title');
        assert.equal(instance.config.textEmpty, config.textEmpty, 'The listBox instance has its own empty list message');
        assert.equal(instance.config.textNumber, config.textNumber, 'The listBox instance has its own number label');
        assert.equal(instance.config.textLoading, config.textLoading, 'The listBox instance has its own loading message');

        assert.equal(instance.getElement().find('h1').text(), config.title, 'The listBox instance has rendered a title with its own content');
        assert.equal(instance.getElement().find('.empty-list').text(), config.textEmpty, 'The listBox instance has rendered a message to display when the list is empty, and set its own content');
        assert.equal(instance.getElement().find('.available-list .label').text(), config.textNumber, 'The listBox instance has rendered a message to show the number of boxes, and set its own content');
        assert.equal(instance.getElement().find('.loading').text(), config.textLoading + '...', 'The listBox instance has rendered a message to show when the component is in loading state, and set its own content');
    });


    QUnit.test('countRenderer', function(assert) {
        var $container = $('#fixture-1');
        var list = [{
            url: 'http://localhost/test',
            label: 'Test',
            text: 'test',
            content: '<b>TEST</b>'
        }, {
            url: 'http://localhost/test2',
            label: 'Test2',
            text: 'test2',
            content: '<b>TEST2</b>',
            width: 4,
            cls: 'myclass'
        }];
        var config = {
            renderTo: $container,
            replace: true,
            list: list,
            countRenderer: function(count) {
                return count - 1;
            }
        };

        var expectedCount = list.length - 1;
        var instance = listBox(config);

        assert.equal(instance.is('rendered'), true, 'The listBox instance must be rendered');
        assert.equal(typeof instance.getElement(), 'object', 'The listBox instance returns the rendered content as an object');
        assert.equal(instance.getElement().length, 1, 'The listBox instance returns the rendered content');
        assert.equal(instance.getElement().parent().get(0), $container.get(0), 'The listBox instance is rendered inside the right container');

        assert.equal(instance.getElement().find('.available-list .count').text(), expectedCount, 'The listBox instance displays the right number of boxes');

        assert.equal(instance.getElement().find('.list .entry').length, list.length, 'The listBox instance has rendered the list of boxes');

        var list2 = list.concat([{
            url: 'http://localhost/test3',
            label: 'Test3',
            text: 'test3',
            content: '<b>TEST3</b>',
            width: 4,
            cls: 'myclass'
        }]);
        instance.update(list2);
        expectedCount = list2.length - 1;

        assert.equal(instance.getElement().find('.available-list .count').text(), expectedCount, 'The listBox instance displays the right number of boxes');

        assert.equal(instance.getElement().find('.list .entry').length, list2.length, 'The listBox instance has rendered the list of boxes');

        instance.destroy();
    });
});
