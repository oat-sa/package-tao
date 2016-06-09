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
    'core/promise',
    'ui/dialog'
], function($, _, Promise, dialog) {
    'use strict';

    QUnit.module('dialog');


    QUnit.test('module', 3, function(assert) {
        assert.equal(typeof dialog, 'function', "The dialog module exposes a function");
        assert.equal(typeof dialog(), 'object', "The dialog factory produces an object");
        assert.notStrictEqual(dialog(), dialog(), "The dialog factory provides a different object on each call");
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
            var instance = dialog();
            assert.equal(typeof instance[data.name], 'function', 'The dialog instance exposes a "' + data.title + '" function');
        });


    QUnit.asyncTest('install', function(assert) {
        var message = 'test';
        var content = '12345';
        var renderTo = '#qunit-fixture';
        var modal = dialog({
            message: message,
            content: content,
            renderTo: renderTo
        });
        var expectedEvents = 4;
        var resolvers = [];
        var promises = _.times(expectedEvents, function() {
            return new Promise(function(resolve) {
                // Extract the resolve function to an array of resolvers
                // because some promised events will occur more than one time.
                // So we need to use anonymous promises, only the quantity matters.
                resolvers.push(resolve);
            });
        });
        var resolve = function() {
            // just resolve one promise
            (resolvers.pop())();
            QUnit.start();
        };

        Promise.all(promises).then(function() {
            modal.destroy();
            assert.ok(null === modal.getDom(), "The dialog instance does not have a DOM element anymore");
            assert.equal($(renderTo).children().length, 0, "The container does not contains the dialog box anymore");

            QUnit.start();
        });

        QUnit.stop(expectedEvents);

        modal.on('opened.modal', function() {
            // this should occur twice
            assert.ok(true, "The dialog box is now visible");
            resolve();
        });
        modal.on('closed.modal', function() {
            // this should occur only once
            assert.ok(true, "The dialog box is now hidden");
            resolve();
        });
        modal.on('create.modal', function() {
            // this should occur only once
            assert.ok(modal.getDom().parent().is(renderTo), "When rendered, the dialog box is rendered into target element");
            resolve();
        });

        assert.equal(typeof modal, 'object', "The dialog instance is an object");
        assert.equal(typeof modal.getDom(), 'object', "The dialog instance gets a DOM element");
        assert.ok(!!modal.getDom().length, "The dialog instance gets a DOM element");
        assert.equal(modal.getDom().parent().length, 0, "The dialog box is not rendered by default");
        assert.equal(modal.getDom().find('.message').text(), message, "The dialog box displays the message");
        assert.equal(modal.getDom().find('.content').text(), content, "The dialog box displays an additional content");

        modal.render();
        modal.hide();
        modal.show();
    });


    QUnit.asyncTest('events', function(assert) {
        var message = 'test';
        var eventRemoved = false;
        var modal = dialog({
            message: message
        });

        QUnit.stop(1);

        modal.on('custom', function() {
            if (eventRemoved) {
                assert.ok(false, "The dialog box has triggered a removed event");
            } else {
                assert.ok(true, "The dialog box has triggered the custom event");
                modal.off('custom');
                eventRemoved = true;
                setTimeout(function() {
                    assert.ok(true, "The dialog box has not triggered the remove event");
                    QUnit.start();

                }, 250);
                modal.trigger('custom');
            }
            QUnit.start();
        });

        assert.equal(typeof modal, 'object', "The dialog instance is an object");
        assert.equal(typeof modal.getDom(), 'object', "The dialog instance gets a DOM element");
        assert.ok(!!modal.getDom().length, "The dialog instance gets a DOM element");
        assert.equal(modal.getDom().parent().length, 0, "The dialog box is not rendered by default");
        assert.equal(modal.getDom().find('.message').text(), message, "The dialog box displays the message");

        modal.trigger('custom');
    });


    QUnit.asyncTest('buttons', function(assert) {
        var message = 'test';
        var modal = dialog({
            message: message,
            buttons: 'yes,no,ok,cancel',
            onYesBtn: function(event, btn) {
                assert.ok('true', '[yes button] The button has been activated');
                assert.equal(typeof btn, 'object', '[yes button] The button descriptor is provided');
                assert.equal(btn.id, 'yes', '[yes button] The right button descriptor is provided');

                QUnit.start();
            },

            onNoBtn: function(event, btn) {
                assert.ok('true', '[no button] The button has been activated');
                assert.equal(typeof btn, 'object', '[no button] The button descriptor is provided');
                assert.equal(btn.id, 'no', '[no button] The right button descriptor is provided');

                QUnit.start();
            },

            onOkBtn: function(event, btn) {
                assert.ok('true', '[ok button] The button has been activated');
                assert.equal(typeof btn, 'object', '[ok button] The button descriptor is provided');
                assert.equal(btn.id, 'ok', '[ok button] The right button descriptor is provided');

                QUnit.start();
            },

            onCancelBtn: function(event, btn) {
                assert.ok('true', '[cancel button] The button has been activated');
                assert.equal(typeof btn, 'object', '[cancel button] The button descriptor is provided');
                assert.equal(btn.id, 'cancel', '[cancel button] The right button descriptor is provided');

                QUnit.start();
            }
        });

        QUnit.stop(6);

        assert.equal(typeof modal, 'object', "The dialog instance is an object");
        assert.equal(typeof modal.getDom(), 'object', "The dialog instance gets a DOM element");
        assert.ok(!!modal.getDom().length, "The dialog instance gets a DOM element");
        assert.equal(modal.getDom().parent().length, 0, "The dialog box is not rendered by default");
        assert.equal(modal.getDom().find('.message').text(), message, "The dialog box displays the message");

        assert.equal(modal.getDom().find('button').length, 4, "The dialog box displays 4 buttons");
        assert.equal(modal.getDom().find('button[data-control="yes"]').length, 1, "The dialog box displays a 'yes' button");
        assert.equal(modal.getDom().find('button[data-control="no"]').length, 1, "The dialog box displays a 'no' button");
        assert.equal(modal.getDom().find('button[data-control="ok"]').length, 1, "The dialog box displays a 'ok' button");
        assert.equal(modal.getDom().find('button[data-control="cancel"]').length, 1, "The dialog box displays a 'cancel' button");

        modal.getDom().find('button[data-control="yes"]').click();
        modal.getDom().find('button[data-control="no"]').click();
        modal.getDom().find('button[data-control="ok"]').click();
        modal.getDom().find('button[data-control="cancel"]').click();


        modal.setButtons({
            id: 'test',
            type: 'info',
            icon: 'test',
            label: 'test'
        }).on('testbtn.modal', function(event, btn) {
            assert.ok('true', '[test button] The button has been activated');
            assert.equal(typeof btn, 'object', '[test button] The button descriptor is provided');
            assert.equal(btn.id, 'test', '[test button] The right button descriptor is provided');

            QUnit.start();
        });

        assert.equal(modal.getDom().find('button').length, 1, "The dialog box displays only 1 button");
        assert.equal(modal.getDom().find('button[data-control="test"]').length, 1, "The dialog box displays a 'test' button");
        assert.equal(modal.getDom().find('button[data-control="test"]').text().trim(), 'test', "The dialog box displays has a 'test' label");
        assert.ok(modal.getDom().find('button[data-control="test"]').hasClass('btn-info'), "The 'test' button has the 'info' class");
        assert.ok(modal.getDom().find('button[data-control="test"]').hasClass('test'), "The 'test' button has the 'test' class");
        assert.equal(modal.getDom().find('button .icon-test').length, 1, "The 'test' button has a 'test' icon");

        modal.getDom().find('button[data-control="test"]').click();


        modal.setButtons(['ok', {
            id: 'done',
            type: 'info',
            icon: 'done',
            label: 'done'
        }]).on('donebtn.modal', function(event, btn) {
            assert.ok('true', '[done button] The button has been activated');
            assert.equal(typeof btn, 'object', '[done button] The button descriptor is provided');
            assert.equal(btn.id, 'done', '[done button] The right button descriptor is provided');

            QUnit.start();
        });

        assert.equal(modal.getDom().find('button').length, 2, "The dialog box displays 2 buttons");
        assert.equal(modal.getDom().find('button[data-control="ok"]').length, 1, "The dialog box displays a 'ok' button");
        assert.equal(modal.getDom().find('button[data-control="done"]').length, 1, "The dialog box displays a 'done' button");
        assert.equal(modal.getDom().find('button[data-control="done"]').text().trim(), 'done', "The dialog box displays has a 'done' label");
        assert.ok(modal.getDom().find('button[data-control="done"]').hasClass('btn-info'), "The 'done' button has the 'info' class");
        assert.ok(modal.getDom().find('button[data-control="done"]').hasClass('done'), "The 'done' button has the 'done' class");
        assert.equal(modal.getDom().find('button .icon-done').length, 1, "The 'done' button has a 'done' icon");

        modal.getDom().find('button[data-control="ok"]').click();
        modal.getDom().find('button[data-control="done"]').click();
    });

    QUnit.asyncTest('destroy', function(assert) {
        QUnit.expect(4);

        var message = 'foo';
        var content = 'bar';
        var renderTo = '#qunit-fixture';

        var modal = dialog({
            message: message,
            content: content,
            renderTo: renderTo
        });

        modal.on('create.modal', function() {
            assert.equal($(renderTo + ' .modal').length, 1, 'The modal element is created');
            assert.equal($(renderTo + ' .message').text(), message, 'The modal message is correct');

            modal.destroy();
        });
        modal.on('destroy.modal', function() {

            assert.equal($(renderTo + ' .modal').length, 1, 'The modal element is still there due to the way the modal works');
            assert.equal(modal.destroyed, true, 'The dialog has the destroyed state');


            QUnit.start();
        });


        modal.render();
    });
    
    QUnit.asyncTest('autoFocusOnOK', function(assert) {
        QUnit.expect(3);

        var message = 'foo';
        var content = 'bar';
        var renderTo = '#qunit-fixture';

        var modal = dialog({
            message: message,
            content: content,
            renderTo: renderTo,
            buttons: 'ok,cancel'
        });

        modal.getDom().find('button[data-control="ok"]').on('focus', function(){
            assert.ok('true', 'Focus on OK button');
            QUnit.start();
        });

        modal.getDom().find('button[data-control="cancel"]').on('focus', function(){
            assert.ok('false', 'Focus on Cancel button');
            QUnit.start();
        });
        
        modal.on('create.modal', function() {
            assert.equal($(renderTo + ' .modal').length, 1, 'The modal element is created');
            assert.equal(modal.getDom().find('button').length, 2, "The dialog box displays 2 buttons");
        });

        modal.render();
    });

    QUnit.asyncTest('autoFocusOnOtherButtons', function(assert) {
        QUnit.expect(3);

        var message = 'foo';
        var content = 'bar';
        var renderTo = '#qunit-fixture';

        var modal = dialog({
            message: message,
            content: content,
            renderTo: renderTo,
            buttons: 'cancel'
        });

        modal.getDom().find('button[data-control="cancel"]').on('focus', function(){
            assert.ok('true', 'Focus on Cancel button');
            QUnit.start();
        });

        modal.on('create.modal', function() {
            assert.equal($(renderTo + ' .modal').length, 1, 'The modal element is created');
            assert.equal(modal.getDom().find('button').length, 1, "The dialog box displays 1 button");
        });

        modal.render();
    });
});
