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
 * Test Runner Navigation Plugin : Previous
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'ui/hider',
    'taoTests/runner/plugin',
    'tpl!taoQtiTest/runner/plugins/navigation/button'
], function ($, __, hider, pluginFactory, buttonTpl){
    'use strict';

    /**
     * Returns the configured plugin
     */
    return pluginFactory({

        name : 'previous',

        /**
         * Initialize the plugin (called during runner's init)
         */
        init : function init(){
            var self = this;

            var testRunner = this.getTestRunner();

            /**
             * Can we move backward ? if not, then we hide the plugin
             */
            var toggle = function toggle(){
                var context = testRunner.getTestContext();
                if(context.navigationMode === 1 && context.canMoveBackward){
                    self.show();
                } else {
                    self.hide();
                }
            };

            //build element (detached)
            this.$element =  $(buttonTpl({
                control : 'move-backward',
                title   : __('Submit and go to the previous item'),
                icon    : 'backward',
                text    : __('Previous')
            }));

            //attach behavior
            this.$element.on('click', function(e){
                e.preventDefault();
                if(self.getState('enabled') !== false){
                    self.disable();

                    testRunner.previous();
                }
            });

            //start disabled
            toggle();
            self.disable();

            //update plugin state based on changes
            testRunner
                .on('loaditem', toggle)
                .on('enablenav', function(){
                    self.enable();
                })
                .on('disablenav', function(){
                    self.disable();
                });
        },

        /**
         * Called during the runner's render phase
         */
        render : function render(){
            var $container = this.getAreaBroker().getNavigationArea();
            $container.append(this.$element);
        },

        /**
         * Called during the runner's destroy phase
         */
        destroy : function destroy (){
            this.$element.remove();
        },

        /**
         * Enable the button
         */
        enable : function enable (){
            this.$element.removeProp('disabled')
                         .removeClass('disabled');
        },

        /**
         * Disable the button
         */
        disable : function disable (){
            this.$element.prop('disabled', true)
                         .addClass('disabled');
        },

        /**
         * Show the button
         */
        show: function show(){
            hider.show(this.$element);
        },

        /**
         * Hide the button
         */
        hide: function hide(){
            hider.hide(this.$element);
        }
    });
});
