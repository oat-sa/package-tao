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
 * Test Runner Navigation Plugin : Next Section
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/hider',
    'taoTests/runner/plugin',
    'taoQtiTest/runner/helpers/messages',
    'tpl!taoQtiTest/runner/plugins/navigation/button'
], function ($, _, __, hider, pluginFactory, messages, buttonTpl){
    'use strict';

    return pluginFactory({
        name : 'nextsection',
        init : function init(){
            var self = this;
            var testRunner = this.getTestRunner();
            var testConfig = testRunner.getTestData().config;

            function toggle(){
                var options = testRunner.getTestContext().options;
                if(testConfig.nextSection && (options.nextSection || options.nextSectionWarning)){
                    self.show();
                } else {
                    self.hide();
                }
            }

            function nextSection() {
                testRunner.next('section');
            }

            this.$element = $(buttonTpl({
                control : 'next-section',
                title   : __('Skip to the next section'),
                icon    : 'fast-forward',
                text    : __('Next Section')
            }));

            this.$element.on('click', function(e){
                var context = testRunner.getTestContext();
                var enable = _.bind(self.enable, self);
                e.preventDefault();
                if(self.getState('enabled') !== false){
                    self.disable();

                    if(context.options.nextSectionWarning){
                        testRunner.trigger(
                            'confirm',
                            messages.getExitMessage(
                                __('After you complete the section it would be impossible to return to this section to make changes. Are you sure you want to end the section?'),
                                'section', testRunner),
                            nextSection, // if the test taker accept
                            enable       // if the test taker refuse
                        );
                    } else {
                        nextSection();
                    }
                }
            });

            this.disable();
            toggle();

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
