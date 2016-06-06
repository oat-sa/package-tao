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
    'core/customEvent'
], function($, customEvent){
    'use strict';

    QUnit.module('API');

    QUnit.test('customEvent api', function(assert){
        assert.ok(typeof customEvent === 'function', "The customEvent module exposes a function");
    });


    QUnit.module('Events');


    QUnit.asyncTest('jQuery', function(assert) {
        var element = $('#elem1');
        var eventName = 'custom';
        var data = 'hello';

        element.on(eventName, function(event) {
            assert.ok(true, 'The event has been triggered');
            assert.equal(event.type, eventName, 'The event has the right name');
            assert.equal(event.originalEvent.detail, data, 'The event has provided the right data');

            QUnit.start();
        });

        customEvent(element.get(0), eventName, data);
    });


    QUnit.asyncTest('native', function(assert) {
        var element = document.getElementById('elem2');
        var eventName = 'custom';
        var data = 'hello';

        element.addEventListener(eventName, function(event) {
            assert.ok(true, 'The event has been triggered');
            assert.equal(event.type, eventName, 'The event has the right name');
            assert.equal(event.detail, data, 'The event has provided the right data');

            QUnit.start();
        });

        customEvent(element, eventName, data);
    });
});
