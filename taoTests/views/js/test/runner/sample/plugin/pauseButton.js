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
 * @author Sam <sam@taotesting.com>
 */
define([
    'jquery',
    'core/promise',
    'taoTests/runner/plugin'
], function ($, Promise, pluginFactory){
    'use strict';

    return pluginFactory({
        name : 'pauseButton',
        init : function init(){
            var self = this;
            var testRunner = this.getTestRunner();

            this.$button = $('<button class="pause"> Pause  </button>');

            this.$button.click(function(){
                if(!testRunner.getState('pause')){
                    testRunner.pause();
                    self.$button.text('Resume');
                } else {
                    testRunner.resume();
                    self.$button.text('Pause');
                }
            });



            self.disable();
            testRunner
                .on('loadItem', function(){
                    self.disable();
                })
                .on('renderitem', function(){
                    self.enable();
                })
                .on('pause', function(){
                    self.getAreaBroker().getContentArea().css('opacity', '0.3');
                })
                .on('resume', function(){
                    self.getAreaBroker().getContentArea().css('opacity', '1');
                });
        },
        render : function render(){

            var $container = this.getAreaBroker().getNavigationArea();
            $container.append(this.$button);

        },
        destroy : function (){
            this.$button.remove();
        },
        enable : function (){
            this.$button.removeProp('disabled');
        },
        disable : function (){
            this.$button.prop('disabled', true);
        }
    });
});
