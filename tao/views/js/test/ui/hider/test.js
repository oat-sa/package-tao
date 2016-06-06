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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */

/**
 * Test the module ui/hider
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'ui/hider'
], function ($, hider) {
    'use strict';

    QUnit.module('hider');

    QUnit.test('module', function (assert) {
        QUnit.expect(1);
        assert.equal(typeof hider, 'object', "The hider module exposes an object");
    });

    QUnit.test('api', function (assert) {
        QUnit.expect(4);
        assert.equal(typeof hider.show, 'function', "The hider module expose the show method");
        assert.equal(typeof hider.hide, 'function', "The hider module expose the hide method");
        assert.equal(typeof hider.toggle, 'function', "The hider module expose the toggle method");
        assert.equal(typeof hider.isHidden, 'function', "The hider module expose the isHidden method");
    });

    QUnit.test('hide', function (assert) {
        QUnit.expect(6);

        var $container = $('#qunit-fixture');
        var $list = $('ul', $container);

        assert.equal($list.length, 1, 'The list exists');
        assert.ok( ! $list.hasClass('hidden'), 'The list has not the hidden class');
        assert.ok( $list.css('display') !== 'none', 'The list is shown');

        assert.deepEqual( hider.hide($list), $list, 'hide chains jquery elements');

        assert.ok( $list.hasClass('hidden'), 'The list has the hidden class');
        assert.equal( $list.css('display'), 'none', 'The list is hidden');

    });

    QUnit.test('show', function (assert) {
        QUnit.expect(6);

        var $container = $('#qunit-fixture');
        var $table = $('table', $container);

        assert.equal($table.length, 1, 'The table exits');

        assert.ok( $table.hasClass('hidden'), 'The table has the hidden class');
        assert.equal( $table.css('display'), 'none', 'The table is hidden');

        assert.deepEqual( hider.show($table), $table, 'show chains jquery elements');

        assert.ok( ! $table.hasClass('hidden'), 'The table has not the hidden class anymore');
        assert.equal( $table.css('display'), 'table', 'The table is shown');
    });

    QUnit.test('toggle', function (assert) {
        QUnit.expect(8);

        var $container = $('#qunit-fixture');
        var $table = $('table', $container);

        assert.equal($table.length, 1, 'The table exits');

        assert.ok( $table.hasClass('hidden'), 'The table has the hidden class');
        assert.equal( $table.css('display'), 'none', 'The table is hidden');

        assert.deepEqual( hider.toggle($table), $table, 'toggle chains jquery elements');

        assert.ok( ! $table.hasClass('hidden'), 'The table has not the hidden class anymore');
        assert.equal( $table.css('display'), 'table', 'The table is shown');

        hider.toggle($table);

        assert.ok( $table.hasClass('hidden'), 'The table has the hidden class');
        assert.equal( $table.css('display'), 'none', 'The table is hidden');
    });

    QUnit.test('isHidden', function (assert) {
        QUnit.expect(10);

        var $container = $('#qunit-fixture');

        var $table = $('table', $container);
        assert.equal($table.length, 1, 'The table exits');
        assert.ok( $table.hasClass('hidden'), 'The table has the hidden class');
        assert.equal( $table.css('display'), 'none', 'The table is hidden');

        assert.equal( hider.isHidden($table), true, 'The table should be hidden');

        var $list = $('ul', $container);
        assert.equal($list.length, 1, 'The list exists');
        assert.ok( ! $list.hasClass('hidden'), 'The list has not the hidden class');
        assert.ok( $list.css('display') !== 'none', 'The list is shown');

        assert.equal( hider.isHidden($list), false, 'The list should not be hidden');

        $list.css('visibility', 'hidden');

        assert.equal( hider.isHidden($list), false, 'The list should still not be hidden - by default isHidden checks only the class');
        assert.equal( hider.isHidden($list, true), true, 'The list is really hidden');

    });
});
