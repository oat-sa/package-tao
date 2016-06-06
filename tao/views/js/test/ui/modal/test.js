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
    'ui/modal',
    'tpl!tao/test/ui/modal/modal',
    'lib/polyfill/performance-now'
], function($, _, Promise, modalListener, modalTpl) {
    'use strict';

    QUnit.module('modal');


    QUnit.test('module', 2, function(assert) {
        assert.equal(typeof modalListener, 'function', "The modal module exposes a function");
        assert.equal(typeof $().modal, 'function', "The modal module inject a jQuery plugin named 'modal'");
    });


    QUnit.asyncTest('install', function(assert) {
        var $container = $('#modal-1');
        var $modal = $(modalTpl());
        $container.append($modal);

        $modal.on('opened.modal', function() {
            assert.ok(true, "The modal is now visible");
            QUnit.start();

            $modal.modal('close');
        });
        $modal.on('closed.modal', function() {
            assert.ok(true, "The modal is now hidden");
            QUnit.start();
        });
        $modal.on('create.modal', function() {
            assert.ok(true, "The modal is created");
            QUnit.start();
        });

        QUnit.stop(2);
        $modal.modal();

        assert.equal($container.children().length, 2, 'The modal append 2 elements');
        assert.equal($container.find('.modal').length, 1, 'The modal is contained');
        assert.equal($container.find('.modal-bg').length, 1, 'The modal appends an overlay');

        assert.ok($modal.is('.modal'), 'The modal has the right class');

        assert.equal($modal.children().length, 2, 'The modal displays 2 elements');
        assert.equal($modal.find('.modal-body').length, 1, 'The modal appends a body');
        assert.equal($modal.find('.modal-close').length, 1, 'The modal appends a close button');
    });


    QUnit.asyncTest('destroy', function(assert) {
        var $container = $('#modal-2');
        var $modal = $(modalTpl());
        $container.append($modal);

        $modal.on('opened.modal', function() {
            assert.ok(true, "The modal is now visible");
            QUnit.start();

            $modal.modal('destroy');
        });
        $modal.on('closed.modal', function() {
            assert.ok(false, "The modal is must not be hidden when destroyed");
        });
        $modal.on('create.modal', function() {
            assert.ok(true, "The modal is created");
            QUnit.start();
        });
        $modal.on('destroyed.modal', function() {
            assert.equal($container.find('.modal-bg').length, 0, 'The modal has removed the overlay');
            assert.ok(true, "The modal is now destroyed");

            QUnit.start();
        });

        QUnit.stop(2);
        $modal.modal();

        assert.equal($container.children().length, 2, 'The modal append 2 elements');
        assert.equal($container.find('.modal').length, 1, 'The modal is contained');
        assert.equal($container.find('.modal-bg').length, 1, 'The modal appends an overlay');

        assert.ok($modal.is('.modal'), 'The modal has the right class');

        assert.equal($modal.children().length, 2, 'The modal displays 2 elements');
        assert.equal($modal.find('.modal-body').length, 1, 'The modal appends a body');
        assert.equal($modal.find('.modal-close').length, 1, 'The modal appends a close button');
    });


    QUnit.asyncTest('events', function(assert) {
        var $container = $('#modal-1');
        var $modal = $(modalTpl());
        var closed = false;
        $container.append($modal);

        $modal.on('opened.modal', function() {
            assert.ok(true, "The modal is now visible");
            QUnit.start();

            if (closed) {
                $modal.modal('destroy');
            } else {
                $modal.modal('close');
            }
        });
        $modal.on('closed.modal', function() {
            assert.ok(true, "The modal is now hidden");
            QUnit.start();

            closed = true;
            $modal.modal('open');
        });
        $modal.on('create.modal', function() {
            assert.ok(true, "The modal is created");
            QUnit.start();
        });
        $modal.on('destroyed.modal', function() {
            assert.equal($container.find('.modal-bg').length, 0, 'The modal has removed the overlay');
            assert.ok(true, "The modal is now destroyed");

            QUnit.start();
        });

        QUnit.stop(4);
        $modal.modal();

        assert.equal($container.children().length, 2, 'The modal append 2 elements');
        assert.equal($container.find('.modal').length, 1, 'The modal is contained');
        assert.equal($container.find('.modal-bg').length, 1, 'The modal appends an overlay');

        assert.ok($modal.is('.modal'), 'The modal has the right class');

        assert.equal($modal.children().length, 2, 'The modal displays 2 elements');
        assert.equal($modal.find('.modal-body').length, 1, 'The modal appends a body');
        assert.equal($modal.find('.modal-close').length, 1, 'The modal appends a close button');
    });
});
