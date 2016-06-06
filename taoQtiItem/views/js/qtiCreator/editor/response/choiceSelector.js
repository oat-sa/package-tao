/*
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/component',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/choiceSelector',
    'select2'
], function($, _, __, component, choiceSelectorTpl){

    'use strict';

    var _defaults = {
            titleLength: 0 // no title
        };

    /**
     * Format option for select2 usage
     *
     * @param state
     * @returns {string}
     */
    function formatOption (state) {
        var title = $(state.element).attr('title');
        return title ? '<span title="' + title + '">' + state.text + '</span>' : state.text;
    }

    /**
     * Add a title to select2 options
     *
     * @param {String} content
     * @param {Number} threshold
     * @returns {*}
     */
    function createOptionTitle (content, threshold) {
        var fullText = $('<div>', { html: content }).text().trim().replace(/\s+/g, ' ');
        var shortText = fullText.substr(0, threshold);
        return fullText.length - shortText.length <= 5 ? fullText : shortText + '…';
    }


    /**
     * Set some additional parameters
     */
    var init = function init(){
        var selected =  _.map(this.config.choices || [], function(c){
            return c.id();
        });
        var choices = this.config.interaction.getChoices();
        var response = this.config.interaction.getResponseDeclaration();
        var config = _.defaults(this.config || {}, _defaults);
        
        config.multiple = response.isCardinality(['multiple', 'ordered']);
        config.options = [];

        _.each(choices, function(choice) {
            var id = choice.id();
            var choiceText = '';
            var option = {
                value: id,
                label: id,
                selected: selected.indexOf(id) > -1
            };
            
            if(choice.is('containerChoice')){
                choiceText = choice.body();
            }else if(choice.is('textVariableChoice')){
                choiceText = choice.val();
            }else{
                return;//not available yet
            }
            
            // 0 as titleLength => no title
            if(config.titleLength) {
                option.title = createOptionTitle(choiceText, config.titleLength);
            }
            config.options.push(option);
        });
    };


    /**
     * Select 2 needs to be removed prior to destruction of the component
     */
    var destroy = function destroy(){
        this.$component.find('select').select2('destroy');
    };


    /**
     * Populate select box and apply select2
     */
    var render = function postRender() {

        var self = this;
        var $selectBox = this.$component.find('select');
        
        $selectBox.select2({
            dropdownAutoWidth: true,
            placeholder: $selectBox.attr('placeholder'),
            minimumResultsForSearch: -1,
            formatResult: formatOption,
            formatSelection: formatOption
        }).on('change', function() {
            var selection = $selectBox.select2('val');
            self.setSelectedChoices(_.isArray(selection) ? selection : [selection]);
            self.trigger('change', self.getSelectedChoices());
        });
    };


    /**
     * @param {Object} config
     * @param {Integer} [config.titleLength] - Number of characters used for the title attribute of an option (may be used loosely)
     *
     */
    var choiceSelectorFactory = function choiceSelectorFactory(config) {
        
        var _selectedChoices = {};
        var choices = {};
        _.each(config.interaction.getChoices(), function(choice){
            choices[choice.id()] = choice;
        });
        
        /**
        * Exposed methods
        * @type {{getChoices: choiceSelector.getChoices, getSelectedChoices: choiceSelector.getSelectedChoices}}
        */
        var choiceSelector = {
            getSelectedChoices : function() {
                return _selectedChoices;
            },
            setSelectedChoices : function(choicesId) {
                _selectedChoices = _.map(choicesId, function(id){
                    return choices[id];
                });
            }
        };
    
        return component(choiceSelector)
                .on('init', init)
                .on('destroy', destroy)
                .on('render', render)
                .setTemplate(choiceSelectorTpl)
                .init(config);
    };

    return choiceSelectorFactory;
});
