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
    'tpl!ui/bulkActionPopup/tpl/layout',
    'ui/component',
    'ui/modal',
    'select2'
], function($, _, __, layoutTpl, component){
    'use strict';

    var _ns = '.bulk-action-popup';

    /**
     * Add the form into a popup and display it
     * @param {Object} instance
     * @param {Object} modalConfig
     * @returns {undefined}
     */
    function initModal(instance, modalConfig){

        instance.getElement()
            .addClass('modal')
            .on('closed.modal', function(){
                //on shot only, on close, destroy the widget
                instance.destroy();
            })
            .modal(modalConfig);
    }

    /**
     * Builds an instance of the bulkActionPopup component
     * 
     * @param {Object} config
     * @param {jQuery} config.renderTo - the jQuery container it should be rendered to
     * @param {String} config.actionName - the action name (use in the title text)
     * @param {String} config.resourceType - the name of the resource type (use in the text)
     * @param {String} [config.resourceTypes] - the name of the resource type in plural (use in the text)
     * @param {Boolean} [config.reason] - defines if the reason section should be displayed or not
     * @param {Function} [config.categoriesSelector] - callback renderer for categories
     * @param {Array} config.allowedResources - list of allowed resources to be displayed
     * @param {Array} [config.deniedResources] - list of denied resources to be displayed
     * @returns {bulkActionPopup}
     */
    return function bulkActionPopupFactory(config){

        //private object to hold the state of edition
        var state = {
            reasons : null,
            comment : ''
        };

        //compute extra config data (essentially for the template)
        config = _.defaults(config, {
            deniedResources : [],
            reason : false,
            resourceCount : config.allowedResources.length,
            single : (config.allowedResources.length === 1),
            resourceTypes : config.resourceType + 's'
        });
        
        return component()
            .setTemplate(layoutTpl)

            // uninstalls the component
            .on('destroy', function(){
                this.getElement().removeClass('modal').modal('destroy');
            })

            // renders the component
            .on('render', function(){

                var self = this;
                var $element = this.getElement();

                initModal(this, {
                    width : (config.single && !config.deniedResources.length && !config.reason) ? 600 : 800
                });

                if ( _.isObject(config.categoriesSelector)) {
                    var $container = $element.find('.reason').children('.categories');
                    config.categoriesSelector.render($container);
                }

                $element.on('selected.cascading-combobox' + _ns, function(e, reasons){
                    state.reasons = reasons;
                    self.trigger('change', state);
                }).on('change' + _ns, 'textarea', function(){
                    state.comment = $(this).val();
                    self.trigger('change', state);
                }).on('click', '.actions .done', function(e){
                    self.trigger('ok', state);
                    self.destroy();
                }).on('click', '.actions .cancel', function(e){
                    e.preventDefault();
                    self.trigger('cancel');
                    self.destroy();
                });
            })
            .init(config);
    };
});