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
 * Test Runner Content Plugin : RubricBlock
 *
 * TODO require mathjax on demand
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'ui/hider',
    'taoTests/runner/plugin',
    'tpl!taoQtiTest/runner/plugins/content/rubricBlock/rubricBlock'
], function ($, __, hider, pluginFactory, containerTpl){
    'use strict';

    /**
     * Ensure the <a> links opens to blank pages
     * @param {jQueryElement} $container - lookup scope
     */
    var blankifyLinks = function blankifyLinks($container){
        $('a', $container).attr('target', '_blank');
    };

    /**
     * Returns the configured plugin
     */
    return pluginFactory({
        name : 'rubricBlock',

        /**
         * Initialize the plugin (called during runner's init)
         */
        init : function init(){
            var self = this;
            var testRunner = this.getTestRunner();

            this.$element = $(containerTpl());

            this.hide();

            //change plugin state
            testRunner
                .on('ready', function(){
                    self.hide();
                })
                .on('loadrubricblock', function(rubrics){
                    if(rubrics){
                        self.$element.html(rubrics);
                        blankifyLinks(self.$element);

                        // notify that the rubric blocks are loaded
                        testRunner.trigger('rubricblock');
                    }
                })
                .on('renderitem', function(){
                    self.show();
                })
                .on('unloaditem', function(){
                    self.hide();
                    self.$element.empty();
                });
        },

        /**
         * Called during the runner's render phase
         */
        render : function render(){

            //attach the element before the content area
            var $container = this.getAreaBroker().getContentArea();
            $container.before(this.$element);
        },

        /**
         * Called during the runner's destroy phase
         */
        destroy : function destroy (){
            this.$element.remove();
        },

        /**
         * Enable the container
         */
        enable : function enable (){
            this.$element.removeProp('disabled')
                         .removeClass('disabled');
        },

        /**
         * Disable the container
         */
        disable : function disable (){
            this.$element.prop('disabled', true)
                         .addClass('disabled');
        },

        /**
         * Show the container
         */
        show: function show(){
            hider.show(this.$element);
        },

        /**
         * Hide the container
         */
        hide: function hide(){
            hider.hide(this.$element);
        }
    });
});
