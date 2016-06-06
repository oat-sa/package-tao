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
    'ui/cascadingComboBox'
], function($, _, cascadingComboBox){
    'use strict';

    QUnit.module('Cascading ComboBox');

    QUnit.test('render (all options)', function(assert){

        var $container = $('#fixture-1');
        var combo =  cascadingComboBox({
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
                    {
                        id : 'optionA',
                        label : 'option A',
                        categories : [
                            {
                                id : 'optionA1',
                                label : 'option A-1',
                                categories : [
                                    {id : 'option A1a', label : 'option A-1-a'},
                                    {id : 'option A1b', label : 'option A-1-b'},
                                    {id : 'option A1c', label : 'option A-1-c'}
                                ]
                            },
                            {
                                id : 'optionA2',
                                label : 'option A-2',
                                categories : [
                                    {id : 'option A2a', label : 'option A-2-a'},
                                    {id : 'option A2b', label : 'option A-2-b'}
                                ]
                            },
                            {
                                label : 'option A-3'
                            }
                        ]
                    },
                    {
                        id : 'optionB',
                        label : 'option B',
                        categories : [
                            {id : 'option B1', label : 'option B-1'},
                            {id : 'option B2', label : 'option B-2'},
                            {id : 'option B3', label : 'option B-3'},
                            {id : 'option B4', label : 'option B-4'}
                        ]
                    },
                    {
                        id : 'option_C',
                        label : 'option C'
                    }
                ]
            });

        var instance = combo.render($container);

        assert.equal($container[0], instance.getContainer()[0], 'container ok');

        assert.equal($container.find('.cascading-combo-box').length, 1, 'initial state ok');

        $container.on('selected.cascading-combobox',function(x,val){
            assert.equal(val.reason1,'optionA','select event fired OK');
        });

        assert.equal($container.find("option[value='optionA1']").length, 0, 'Cascaded option not yet added to DOM');
        $container.find('select').eq(0).find('option[value=optionA]').prop('selected', 'selected');
        $container.find('select').eq(0).trigger('change');

        $container.off('selected.cascading-combobox');

        assert.equal($container.find('.cascading-combo-box').length, 2, 'increasing cascading OK');

        $container.find('select').eq(1).find('option[value=optionA1]').prop('selected', 'selected');
        $container.find('select').eq(1).trigger('change');
        assert.equal($container.find("option[value='optionA1']").length, 1, 'option added to DOM');
        assert.equal($container.find('.cascading-combo-box').length, 3, 'cascading OK');

        $container.find('select').eq(0).find('option[value=optionB]').prop('selected', 'selected');
        $container.find('select').eq(0).trigger('change');
        assert.equal($container.find('.cascading-combo-box').length, 2, 'removing cascading OK');
        assert.equal($container.find("option[value='optionA1']").length, 0, 'removing cascading OK');
    });

});
