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
define(['ui/dialog/alert'], function(dialogAlert) {
    'use strict';

    QUnit.module('dialog/alert');


    QUnit.test('module', 3, function(assert) {
        var alert1 = dialogAlert();
        var alert2 = dialogAlert();
        assert.equal(typeof dialogAlert, 'function', "The dialogAlert module exposes a function");
        assert.equal(typeof alert1, 'object', "The dialogAlert factory produces an object");
        assert.notStrictEqual(alert1, alert2, "The dialogAlert factory provides a different object on each call");
        alert1.destroy();
        alert2.destroy();
    });


    var dialogApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'setButtons', title : 'setButtons' },
        { name : 'render', title : 'render' },
        { name : 'show', title : 'show' },
        { name : 'hide', title : 'hide' },
        { name : 'trigger', title : 'trigger' },
        { name : 'on', title : 'on' },
        { name : 'off', title : 'off' },
        { name : 'getDom', title : 'getDom' }
    ];

    QUnit
        .cases(dialogApi)
        .test('instance API ', function(data, assert) {
            var instance = dialogAlert();
            assert.equal(typeof instance[data.name], 'function', 'The dialogAlert instance exposes a "' + data.title + '" function');
            instance.destroy();
        });


    QUnit.asyncTest('use', function(assert) {
        var message = 'test';
        var action = function() {
            assert.ok(true, 'The dialogAlert has triggered the callback function when closing!');
            QUnit.start();
        };
        var modal = dialogAlert(message, action);

        assert.equal(typeof modal, 'object', "The dialogAlert instance is an object");
        assert.equal(typeof modal.getDom(), 'object', "The dialogAlert instance gets a DOM element");
        assert.ok(!!modal.getDom().length, "The dialogAlert instance gets a DOM element");
        assert.equal(modal.getDom().parent().length, 1, "The dialogAlert box is rendered by default");
        assert.equal(modal.getDom().find('.message').text(), message, "The dialogAlert box displays the message");

        assert.equal(modal.getDom().find('button').length, 1, "The dialogAlert box displays a unique button");
        assert.equal(modal.getDom().find('button[data-control="ok"]').length, 1, "The dialogAlert box displays a 'ok' button");

        modal.getDom().find('button[data-control="ok"]').click();
    });

});
