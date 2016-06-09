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
    'ui/breadcrumbs'
], function($, _, breadcrumbs) {
    'use strict';

    QUnit.module('breadcrumbs');


    QUnit.test('module', 3, function(assert) {
        assert.equal(typeof breadcrumbs, 'function', "The breadcrumbs module exposes a function");
        assert.equal(typeof breadcrumbs(), 'object', "The breadcrumbs factory produces an object");
        assert.notStrictEqual(breadcrumbs(), breadcrumbs(), "The breadcrumbs factory provides a different object on each call");
    });


    var testReviewApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'render', title : 'render' },
        { name : 'show', title : 'show' },
        { name : 'hide', title : 'hide' },
        { name : 'enable', title : 'enable' },
        { name : 'disable', title : 'disable' },
        { name : 'is', title : 'is' },
        { name : 'setState', title : 'setState' },
        { name : 'getElement', title : 'getElement' },
        { name : 'getContainer', title : 'getContainer' },
        { name : 'getTemplate', title : 'getTemplate' },
        { name : 'setTemplate', title : 'setTemplate' }
    ];

    QUnit
        .cases(testReviewApi)
        .test('instance API ', function(data, assert) {
            var instance = breadcrumbs();
            assert.equal(typeof instance[data.name], 'function', 'The breadcrumbs instance exposes a "' + data.title + '" function');
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
        var instance = breadcrumbs(config);

        assert.notEqual(instance.config, config, 'The breadcrumbs instance must duplicate the config set');
        assert.equal(instance.hasOwnProperty('nothing'), false, 'The breadcrumbs instance must not accept undefined config properties');
        assert.equal(instance.hasOwnProperty('dummy'), false, 'The breadcrumbs instance must not accept null config properties');
        assert.equal(instance.is('rendered'), false, 'The breadcrumbs instance must not be rendered');

        instance.destroy();
    });


    var breadcrumbsEntries = [{
        id: 'home',
        url: 'http://localhost/home',
        label: 'Home'
    }, {
        id: 'page1',
        url: 'http://localhost/page1',
        label: 'Page1',
        entries: [{
            id: 'page2',
            url: 'http://localhost/page2',
            label: 'Page2'
        }, {
            id: 'page3',
            url: 'http://localhost/page3',
            label: 'Page3'
        }]
    }, {
        id: 'current',
        url: 'http://localhost/current',
        label: 'Current',
        data: 'context'
    }];

    var breadcrumbsEntries2 = [{
        id: 'home',
        url: 'http://localhost/home',
        label: 'Home'
    }, {
        id: 'page2',
        url: 'http://localhost/page2',
        label: 'Page2',
        entries: [{
            id: 'page1',
            url: 'http://localhost/page1',
            label: 'Page1'
        }, {
            id: 'page3',
            url: 'http://localhost/page3',
            label: 'Page3'
        }]
    }, {
        id: 'other',
        url: 'http://localhost/other',
        label: 'Other',
        data: 'context'
    }];


    QUnit.test('render', function(assert) {
        var $dummy = $('<div class="dummy" />');
        var $container = $('#fixture-1').append($dummy);
        var config = {
            renderTo: $container,
            replace: true,
            breadcrumbs: breadcrumbsEntries
        };
        var instance;

        assert.equal($container.children().length, 1, 'The container already contains an element');
        assert.equal($container.children().get(0), $dummy.get(0), 'The container contains the dummy element');
        assert.equal($container.find('.dummy').length, 1, 'The container contains an element of the class dummy');

        instance = breadcrumbs(config);

        assert.equal($container.find('.dummy').length, 0, 'The container does not contain an element of the class dummy');
        assert.equal(instance.is('rendered'), true, 'The breadcrumbs instance must be rendered');
        assert.equal(typeof instance.getElement(), 'object', 'The breadcrumbs instance returns the rendered content as an object');
        assert.equal(instance.getElement().length, 1, 'The breadcrumbs instance returns the rendered content');
        assert.equal(instance.getElement().parent().get(0), $container.get(0), 'The breadcrumbs instance is rendered inside the right container');

        assert.equal(instance.getElement().find('> li').length, breadcrumbsEntries.length, 'The breadcrumbs instance has rendered the list of entries');

        // 1st
        assert.equal(instance.getElement().find('> li').first().data('breadcrumb'), breadcrumbsEntries[0].id, 'The 1st breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').first().find('a').text(), breadcrumbsEntries[0].label, 'The 1st breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').first().find('a').attr('href'), breadcrumbsEntries[0].url, 'The 1st breadcrumb has the right URL');

        // 2nd
        assert.equal(instance.getElement().find('> li').eq(1).data('breadcrumb'), breadcrumbsEntries[1].id, 'The 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').text(), breadcrumbsEntries[1].label, 'The 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').attr('href'), breadcrumbsEntries[1].url, 'The 2nd breadcrumb has the right URL');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').length, breadcrumbsEntries[1].entries.length, 'The 2nd breadcrumb has a sub list');

        // 2nd sub 1
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).data('breadcrumb'), breadcrumbsEntries[1].entries[0].id, 'The 1st list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').text(), breadcrumbsEntries[1].entries[0].label, 'The 1st list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').attr('href'), breadcrumbsEntries[1].entries[0].url, 'The 1st list entry of the 2nd breadcrumb has the right URL');

        // 2nd sub 2
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).data('breadcrumb'), breadcrumbsEntries[1].entries[1].id, 'The 2nd list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').text(), breadcrumbsEntries[1].entries[1].label, 'The 2nd list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').attr('href'), breadcrumbsEntries[1].entries[1].url, 'The 2nd list entry of the 2nd breadcrumb has the right URL');

        // 3rd
        assert.equal(instance.getElement().find('> li').eq(2).data('breadcrumb'), breadcrumbsEntries[2].id, 'The 3rd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(2).find('.a').text(), breadcrumbsEntries[2].label + ' - ' + breadcrumbsEntries[2].data, 'The 3rd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(2).find('a').length, 0, 'The 3rd breadcrumb does not have a link');

        instance.destroy();

        assert.equal($container.children().length, 0, 'The container is now empty');
        assert.equal(instance.getElement(), null, 'The breadcrumbs instance has removed its rendered content');
    });


    QUnit.test('update', function(assert) {
        var $container = $('#fixture-2');
        var config = {
            renderTo: $container,
            replace: true,
            breadcrumbs: breadcrumbsEntries
        };
        var instance = breadcrumbs();

        assert.equal(instance.is('rendered'), false, 'The breadcrumbs instance must not be rendered');
        assert.equal(instance.getElement(), null, 'The breadcrumbs instance must not have DOM element');

        /*** 1ST PASS - WITHOUT CONTAINER - EXPLICIT RENDERING ***/
        instance.update(breadcrumbsEntries);

        assert.equal(instance.is('rendered'), true, '[1st pass] The breadcrumbs instance must be rendered');
        assert.equal(instance.getElement().length, 1, '[1st pass] The breadcrumbs instance must have DOM element');

        assert.equal(instance.getElement().find('> li').length, breadcrumbsEntries.length, '[1st pass] The breadcrumbs instance has rendered the list of entries');

        // 1st
        assert.equal(instance.getElement().find('> li').first().data('breadcrumb'), breadcrumbsEntries[0].id, '[1st pass] The 1st breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').first().find('a').text(), breadcrumbsEntries[0].label, '[1st pass] The 1st breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').first().find('a').attr('href'), breadcrumbsEntries[0].url, '[1st pass] The 1st breadcrumb has the right URL');

        // 2nd
        assert.equal(instance.getElement().find('> li').eq(1).data('breadcrumb'), breadcrumbsEntries[1].id, '[1st pass] The 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').text(), breadcrumbsEntries[1].label, '[1st pass] The 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').attr('href'), breadcrumbsEntries[1].url, '[1st pass] The 2nd breadcrumb has the right URL');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').length, breadcrumbsEntries[1].entries.length, '[1st pass] The 2nd breadcrumb has a sub list');

        // 2nd sub 1
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).data('breadcrumb'), breadcrumbsEntries[1].entries[0].id, '[1st pass] The 1st list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').text(), breadcrumbsEntries[1].entries[0].label, '[1st pass] The 1st list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').attr('href'), breadcrumbsEntries[1].entries[0].url, '[1st pass] The 1st list entry of the 2nd breadcrumb has the right URL');

        // 2nd sub 2
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).data('breadcrumb'), breadcrumbsEntries[1].entries[1].id, '[1st pass] The 2nd list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').text(), breadcrumbsEntries[1].entries[1].label, '[1st pass] The 2nd list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').attr('href'), breadcrumbsEntries[1].entries[1].url, '[1st pass] The 2nd list entry of the 2nd breadcrumb has the right URL');

        // 3rd
        assert.equal(instance.getElement().find('> li').eq(2).data('breadcrumb'), breadcrumbsEntries[2].id, '[1st pass] The 3rd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(2).find('.a').text(), breadcrumbsEntries[2].label + ' - ' + breadcrumbsEntries[2].data, '[1st pass] The 3rd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(2).find('a').length, 0, '[1st pass] The 3rd breadcrumb does not have a link');

        /*** 2ND PASS - WITHOUT CONTAINER - EXPLICIT RENDERING ***/
        instance.update(breadcrumbsEntries2);

        assert.equal(instance.is('rendered'), true, '[2nd pass] The breadcrumbs instance must be rendered');
        assert.equal(instance.getElement().length, 1, '[2nd pass] The breadcrumbs instance must have DOM element');

        assert.equal(instance.getElement().find('> li').length, breadcrumbsEntries2.length, '[2nd pass] The breadcrumbs instance has rendered the list of entries');

        // 1st
        assert.equal(instance.getElement().find('> li').first().data('breadcrumb'), breadcrumbsEntries2[0].id, '[2nd pass] The 1st breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').first().find('a').text(), breadcrumbsEntries2[0].label, '[2nd pass] The 1st breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').first().find('a').attr('href'), breadcrumbsEntries2[0].url, '[2nd pass] The 1st breadcrumb has the right URL');

        // 2nd
        assert.equal(instance.getElement().find('> li').eq(1).data('breadcrumb'), breadcrumbsEntries2[1].id, '[2nd pass] The 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').text(), breadcrumbsEntries2[1].label, '[2nd pass] The 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').attr('href'), breadcrumbsEntries2[1].url, '[2nd pass] The 2nd breadcrumb has the right URL');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').length, breadcrumbsEntries2[1].entries.length, '[2nd pass] The 2nd breadcrumb has a sub list');

        // 2nd sub 1
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).data('breadcrumb'), breadcrumbsEntries2[1].entries[0].id, '[2nd pass] The 1st list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').text(), breadcrumbsEntries2[1].entries[0].label, '[2nd pass] The 1st list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').attr('href'), breadcrumbsEntries2[1].entries[0].url, '[2nd pass] The 1st list entry of the 2nd breadcrumb has the right URL');

        // 2nd sub 2
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).data('breadcrumb'), breadcrumbsEntries2[1].entries[1].id, '[2nd pass] The 2nd list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').text(), breadcrumbsEntries2[1].entries[1].label, '[2nd pass] The 2nd list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').attr('href'), breadcrumbsEntries2[1].entries[1].url, '[2nd pass] The 2nd list entry of the 2nd breadcrumb has the right URL');

        // 3rd
        assert.equal(instance.getElement().find('> li').eq(2).data('breadcrumb'), breadcrumbsEntries2[2].id, '[2nd pass] The 3rd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(2).find('.a').text(), breadcrumbsEntries2[2].label + ' - ' + breadcrumbsEntries2[2].data, '[2nd pass] The 3rd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(2).find('a').length, 0, '[2nd pass] The 3rd breadcrumb does not have a link');

        instance.destroy();


        /*** 3RD PASS - INSIDE CONTAINER - IMPLICIT RENDERING ***/
        assert.equal($container.children().length, 0, '[3rd pass] The container does not contain any element');

        instance = breadcrumbs(config);

        assert.equal(instance.is('rendered'), true, '[3rd pass] The breadcrumbs instance must be rendered');
        assert.equal(typeof instance.getElement(), 'object', '[3rd pass] The breadcrumbs instance returns the rendered content as an object');
        assert.equal(instance.getElement().length, 1, '[3rd pass] The breadcrumbs instance returns the rendered content');
        assert.equal(instance.getElement().parent().get(0), $container.get(0), '[3rd pass] The breadcrumbs instance is rendered inside the right container');
        assert.equal(instance.getElement().get(0), $container.children().get(0), '[3rd pass] The breadcrumbs instance is rendered inside the right container and is the only child');

        assert.equal(instance.getElement().find('> li').length, breadcrumbsEntries.length, '[3rd pass] The breadcrumbs instance has rendered the list of entries');

        // 1st
        assert.equal(instance.getElement().find('> li').first().data('breadcrumb'), breadcrumbsEntries[0].id, '[3rd pass] The 1st breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').first().find('a').text(), breadcrumbsEntries[0].label, '[3rd pass] The 1st breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').first().find('a').attr('href'), breadcrumbsEntries[0].url, '[3rd pass] The 1st breadcrumb has the right URL');

        // 2nd
        assert.equal(instance.getElement().find('> li').eq(1).data('breadcrumb'), breadcrumbsEntries[1].id, '[3rd pass] The 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').text(), breadcrumbsEntries[1].label, '[3rd pass] The 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').attr('href'), breadcrumbsEntries[1].url, '[3rd pass] The 2nd breadcrumb has the right URL');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').length, breadcrumbsEntries[1].entries.length, '[3rd pass] The 2nd breadcrumb has a sub list');

        // 2nd sub 1
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).data('breadcrumb'), breadcrumbsEntries[1].entries[0].id, '[3rd pass] The 1st list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').text(), breadcrumbsEntries[1].entries[0].label, '[3rd pass] The 1st list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').attr('href'), breadcrumbsEntries[1].entries[0].url, '[3rd pass] The 1st list entry of the 2nd breadcrumb has the right URL');

        // 2nd sub 2
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).data('breadcrumb'), breadcrumbsEntries[1].entries[1].id, '[3rd pass] The 2nd list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').text(), breadcrumbsEntries[1].entries[1].label, '[3rd pass] The 2nd list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').attr('href'), breadcrumbsEntries[1].entries[1].url, '[3rd pass] The 2nd list entry of the 2nd breadcrumb has the right URL');

        // 3rd
        assert.equal(instance.getElement().find('> li').eq(2).data('breadcrumb'), breadcrumbsEntries[2].id, '[3rd pass] The 3rd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(2).find('.a').text(), breadcrumbsEntries[2].label + ' - ' + breadcrumbsEntries[2].data, '[3rd pass] The 3rd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(2).find('a').length, 0, '[3rd pass] The 3rd breadcrumb does not have a link');

        /*** 4TH PASS - INSIDE CONTAINER - EXPLICIT RENDERING ***/
        instance.update(breadcrumbsEntries2);

        assert.equal(instance.is('rendered'), true, '[4th pass] The breadcrumbs instance must be rendered');
        assert.equal(typeof instance.getElement(), 'object', '[4th pass] The breadcrumbs instance returns the rendered content as an object');
        assert.equal(instance.getElement().length, 1, '[4th pass] The breadcrumbs instance returns the rendered content');
        assert.equal(instance.getElement().parent().get(0), $container.get(0), '[4th pass] The breadcrumbs instance is rendered inside the right container');
        assert.equal(instance.getElement().get(0), $container.children().get(0), '[3rd pass] The breadcrumbs instance is rendered inside the right container and is the only child');

        assert.equal(instance.getElement().find('> li').length, breadcrumbsEntries2.length, '[4th pass] The breadcrumbs instance has rendered the list of entries');

        // 1st
        assert.equal(instance.getElement().find('> li').first().data('breadcrumb'), breadcrumbsEntries2[0].id, '[4th pass] The 1st breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').first().find('a').text(), breadcrumbsEntries2[0].label, '[4th pass] The 1st breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').first().find('a').attr('href'), breadcrumbsEntries2[0].url, '[4th pass] The 1st breadcrumb has the right URL');

        // 2nd
        assert.equal(instance.getElement().find('> li').eq(1).data('breadcrumb'), breadcrumbsEntries2[1].id, '[4th pass] The 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').text(), breadcrumbsEntries2[1].label, '[4th pass] The 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('> a').attr('href'), breadcrumbsEntries2[1].url, '[4th pass] The 2nd breadcrumb has the right URL');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').length, breadcrumbsEntries2[1].entries.length, '[4th pass] The 2nd breadcrumb has a sub list');

        // 2nd sub 1
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).data('breadcrumb'), breadcrumbsEntries2[1].entries[0].id, '[4th pass] The 1st list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').text(), breadcrumbsEntries2[1].entries[0].label, '[4th pass] The 1st list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(0).find('a').attr('href'), breadcrumbsEntries2[1].entries[0].url, '[4th pass] The 1st list entry of the 2nd breadcrumb has the right URL');

        // 2nd sub 2
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).data('breadcrumb'), breadcrumbsEntries2[1].entries[1].id, '[4th pass] The 2nd list entry of the 2nd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').text(), breadcrumbsEntries2[1].entries[1].label, '[4th pass] The 2nd list entry of the 2nd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(1).find('li').eq(1).find('a').attr('href'), breadcrumbsEntries2[1].entries[1].url, '[4th pass] The 2nd list entry of the 2nd breadcrumb has the right URL');

        // 3rd
        assert.equal(instance.getElement().find('> li').eq(2).data('breadcrumb'), breadcrumbsEntries2[2].id, '[4th pass] The 3rd breadcrumb has the right identifier');
        assert.equal(instance.getElement().find('> li').eq(2).find('.a').text(), breadcrumbsEntries2[2].label + ' - ' + breadcrumbsEntries2[2].data, '[4th pass] The 3rd breadcrumb has the right label');
        assert.equal(instance.getElement().find('> li').eq(2).find('a').length, 0, '[4th pass] The 3rd breadcrumb does not have a link');

        instance.destroy();

        assert.equal($container.children().length, 0, '[4th pass] The container is now empty');
        assert.equal(instance.getElement(), null, '[4th pass] The breadcrumbs instance has removed its rendered content');
    });


    QUnit.test('show/hide', function(assert) {
        var instance = breadcrumbs()
                        .render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The breadcrumbs instance must be rendered');
        assert.equal($component.length, 1, 'The breadcrumbs instance returns the rendered content');

        assert.equal(instance.is('hidden'), false, 'The breadcrumbs instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The breadcrumbs instance does not have the hidden class');

        instance.hide();

        assert.equal(instance.is('hidden'), true, 'The breadcrumbs instance is hidden');
        assert.equal(instance.getElement().hasClass('hidden'), true, 'The breadcrumbs instance has the hidden class');

        instance.show();

        assert.equal(instance.is('hidden'), false, 'The breadcrumbs instance is visible');
        assert.equal(instance.getElement().hasClass('hidden'), false, 'The breadcrumbs instance does not have the hidden class');

        instance.destroy();
    });


    QUnit.test('enable/disable', function(assert) {
        var instance = breadcrumbs()
                        .render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The breadcrumbs instance must be rendered');
        assert.equal($component.length, 1, 'The breadcrumbs instance returns the rendered content');

        assert.equal(instance.is('disabled'), false, 'The breadcrumbs instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The breadcrumbs instance does not have the disabled class');

        instance.disable();

        assert.equal(instance.is('disabled'), true, 'The breadcrumbs instance is disabled');
        assert.equal(instance.getElement().hasClass('disabled'), true, 'The breadcrumbs instance has the disabled class');

        instance.enable();

        assert.equal(instance.is('disabled'), false, 'The breadcrumbs instance is enabled');
        assert.equal(instance.getElement().hasClass('disabled'), false, 'The breadcrumbs instance does not have the disabled class');

        instance.destroy();
    });


    QUnit.test('state', function(assert) {
        var instance = breadcrumbs()
                        .render();
        var $component = instance.getElement();

        assert.equal(instance.is('rendered'), true, 'The breadcrumbs instance must be rendered');
        assert.equal($component.length, 1, 'The breadcrumbs instance returns the rendered content');

        assert.equal(instance.is('customState'), false, 'The breadcrumbs instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The breadcrumbs instance does not have the customState class');

        instance.setState('customState', true);

        assert.equal(instance.is('customState'), true, 'The breadcrumbs instance has the customState state');
        assert.equal(instance.getElement().hasClass('customState'), true, 'The breadcrumbs instance has the customState class');

        instance.setState('customState', false);

        assert.equal(instance.is('customState'), false, 'The breadcrumbs instance does not have the customState state');
        assert.equal(instance.getElement().hasClass('customState'), false, 'The breadcrumbs instance does not have the customState class');

        instance.destroy();
    });
});
