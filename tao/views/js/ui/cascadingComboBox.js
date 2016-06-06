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
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/component',
    'tpl!ui/bulkActionPopup/tpl/select',
    'select2'
], function($, _, __, component, selectTpl) {
    'use strict';

    var selectedValues = {};

    /**
     * Create a combobox and initialize it with select2
     *
     * @param {Number} level
     * @param {array} categoriesDefinitions - the array that defines the number and config for each level of combobox cascade
     * @param {array} categories - the array that contains nested array of categories
     * @returns {jQuery}
     */
    function createCombobox(level, categoriesDefinitions, categories){
        if(categoriesDefinitions[level]){
            var categoryDef = categoriesDefinitions[level];
            var _categories, $comboBox;
            if(categoryDef.id){

                //format categories
                _categories = _.map(categories, function(cat){
                    var _cat = _.clone(cat);
                    if(_cat.categories){
                        //encode subcategory in json
                        _cat.categories = JSON.stringify(_cat.categories);
                    }
                    return _cat;
                });

                //init <select> DOM element
                $comboBox = $(selectTpl({
                    comboboxId : categoryDef.id,
                    comboboxLabel : categoryDef.label || '',
                    options : _categories
                }));

                //add event handler
                $comboBox.on('change', function(){

                    var subCategories, $subComboBox;
                    var $selected = $comboBox.find(":selected");
                    selectedValues[categoryDef.id] = $selected.val();

                    //clean previously created combo boxes
                    $comboBox.nextAll('.cascading-combo-box').remove();

                    //trigger event
                    $comboBox.trigger('selected.cascading-combobox', [selectedValues]);

                    subCategories = $selected.data('categories');
                    if(_.isArray(subCategories) && subCategories.length){
                        //init sub-level select box by recursive call to createCombobox
                        $subComboBox = createCombobox(level + 1, categoriesDefinitions, subCategories);
                        if($subComboBox){
                            $comboBox.after($subComboBox);
                        }
                    }
                });

                //init select 2 on $comboBox
                $comboBox.find('select').select2({
                    dropdownAutoWidth : true,
                    placeholder : categoryDef.placeholder || __('select...'),
                    minimumResultsForSearch : -1
                });

                return $comboBox;
            }
        }else{
            throw 'missing category definition on level : ' + level;
        }
    }

    /**
     * @param {object} options
     * @param {Array} [options.categoriesDefinitions] - the array that defines the number and config for each level of combobox cascade
     * @param {Array} [options.categories] - the array that contains nested array of categories
     * @returns {function}
     */
    return function cascadingComboBoxFactory(options) {

        return component()
            .on('render', function render($container) {
                if (_.isArray(options.categoriesDefinitions) && _.isArray(options.categories)) {
                    var $comboBox = createCombobox(0, options.categoriesDefinitions, options.categories);
                    $container.append($comboBox);
                }
            })
            .init(options);
    };

});