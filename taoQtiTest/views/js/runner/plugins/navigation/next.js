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
 * Test Runner Navigation Plugin : Next
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
     * The display of the next button
     */
    var buttonData = {
        next : {
            control : 'move-forward',
            title   : __('Submit and go to the next item'),
            icon    : 'forward',
            text    : __('Next')
        },
        end : {
            control : 'move-end',
            title   : __('Submit and go to the end of the test'),
            icon    : 'fast-forward',
            text    : __('End test')
        }
    };

    /**
     * Create the button based on the current context
     * @param {Object} context - the test context
     * @returns {jQueryElement} the button
     */
    var createElement = function createElement(context){
        var dataType = !!context.isLast ? 'end' : 'next';
        return $(buttonTpl(buttonData[dataType]));
    };

    /**
     * Update the button based on the context
     * @param {jQueryElement} $element - the element to update
     * @param {Object} context - the test context
     */
    var updateElement = function updateElement($element, context){
        var dataType = !!context.isLast ? 'end' : 'next';
        if($element.data('control') !== buttonData[dataType].control){

            $element.data('control', buttonData[dataType].control)
                    .attr('title', buttonData[dataType].title)
                    .find('.text').text(buttonData[dataType].text);

            if(dataType === 'next'){
                $element.find('.icon-' + buttonData.end.icon)
                        .removeClass('icon-' + buttonData.end.icon)
                        .addClass('icon-' + buttonData.next.icon);
            } else {
                $element.find('.icon-' + buttonData.next.icon)
                        .removeClass('icon-' + buttonData.next.icon)
                        .addClass('icon-' + buttonData.end.icon);
            }
        }
    };

    /**
     * Returns the configured plugin
     */
    return pluginFactory({
        name : 'next',

        /**
         * Initialize the plugin (called during runner's init)
         */
        init : function init(){
            var self = this;
            var testRunner = this.getTestRunner();

            //create the button (detached)
            this.$element = createElement(testRunner.getTestContext());

            //plugin behavior
            this.$element.on('click', function(e){
                e.preventDefault();

                if(self.getState('enabled') !== false){
                    self.disable();
                    if($(this).data('control') === 'move-end'){
                        self.trigger('end');
                    }
                    testRunner.next();
                }
            });

            //disabled by default
            this.disable();

            //change plugin state
            testRunner
                .on('loaditem', function(){
                    updateElement(self.$element, testRunner.getTestContext());
                })
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

            //attach the element to the navigation area
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
