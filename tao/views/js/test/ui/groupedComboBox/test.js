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
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */
define([
    'jquery',
    'lodash',
    'ui/groupedComboBox'
], function($, _, groupedComboBox){
    'use strict';

    var selectEntries = {
        categoriesDefinitions: [
            {
                id: 'reason1',
                placeholder: 'Reason 1'
            },
            {
                id: 'reason2',
                placeholder: 'Reason 2'
            },
            {
                id: 'reason3',
                placeholder: 'Reason 3'
            }
        ],
        categories : [
            [
                {id : 'optionA1', label : 'option A-1'},
                {id : 'optionA2', label : 'option A-2'},
                {id : 'optionA3', label : 'option A-3'}
            ],
            [
                {id : 'optionB1', label : 'option B-1'},
                {id : 'optionB2', label : 'option B-2'},
                {id : 'optionB3', label : 'option B-3'},
                {id : 'optionB4', label : 'option B-4'}
            ],
            [   {id : 'optionC3', label : 'option C-3'},
                {id : 'optionC4', label : 'option C-4'}]
        ]
    };

    QUnit.module('GroupedComboBox');

    QUnit.test('API', function(assert) {
        QUnit.expect(2);
        var combo =  groupedComboBox();
        assert.ok(typeof combo === 'object', 'The GroupedComboBox function creates an object');
        assert.ok(typeof combo.render === 'function', 'The GroupedComboBox instance has a render method');
    });

    QUnit.test('render', function(assert) {

        QUnit.expect(3);

        var $container = $('#fixture-1');
        var combo = groupedComboBox(selectEntries);

        var instance = combo.render($container);

        assert.equal($container[0], instance.getContainer()[0], 'container ok');

        assert.equal($container.find('.cascading-combo-box').length, 3, 'initial state ok');

        assert.equal($container.find("option[value='optionA1']").length, 1, 'all option already added to DOM');
    });


    QUnit.test('behavior', function(assert) {
        QUnit.expect(8);

        var $container = $('#fixture-1');

        groupedComboBox(selectEntries).render($container);

        $container.on('selected.cascading-combobox', function (x, val) {
            assert.equal(val.reason1, 'optionA1', 'select event fired OK');
        });

        $container.find('select').eq(0).find('option[value=optionA1]').prop('selected', 'selected');
        $container.find('select').eq(0).trigger('change');
        assert.equal($container.find('.cascading-combo-box').length, 3, 'selection doesn\'t affect other elements OK');
        $container.off('selected.cascading-combobox');

        $container.find('select').eq(1).find('option[value=optionB1]').prop('selected', 'selected');
        $container.find('select').eq(1).trigger('change');
        assert.equal($container.find("option[value='optionA1']").length, 1, 'option still added to DOM');
        assert.equal($container.find('.cascading-combo-box').length, 3, 'selection doesn\'t affect other elements OK');

        $container.find('select').eq(0).find('option[value=optionA2]').prop('selected', 'selected');
        $container.find('select').eq(0).trigger('change');
        assert.equal($container.find('.cascading-combo-box').length, 3, 'all options stands in DOM afterwards OK');
        assert.equal($container.find("option[value='optionA1']").length, 1, 'all options stands in DOM afterwards OK');


        $container.on('selected.cascading-combobox', function (x, val) {
            assert.equal(val.reason2, '', 'select event on clear fired OK');
        });
        $container.find('.select2-search-choice-close').eq(1).trigger('mousedown');
        assert.equal($container.find('select').eq(1).find(':selected').val(), '', 'option clearing OK');
        $container.off('selected.cascading-combobox');

    });



    QUnit.test('initial values', function(assert) {
        QUnit.expect(1);
        var $container = $('#fixture-1');

        var options = _.extend(selectEntries,{selected:['optionA2']});
        groupedComboBox(options).render($container);

        assert.equal($container.find('select').eq(0).find(':selected').val(), 'optionA2', 'option clearing OK');


    });

});
