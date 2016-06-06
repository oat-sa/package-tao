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
        name : 'timer',
        init : function init(){
            var self = this;

            var config = this.getConfig();
            var testRunner = this.getTestRunner();

            var start = function(){
                var $timer = $('.timer', self.$element);
                if(!self.interval){
                    self.interval = setInterval(function(){
                        $timer.text(++self.time);
                    }, 1000);
                }
            };
            var stop  = function(interval){
                var $timer = $('.timer', self.$element);
                if(self.interval){
                    clearInterval(self.interval);
                    self.interval = null;
                }
            };

            this.$element = $('<p>Elapsed : <span class="timer">00</span>s</p>');
            this.time = 0;

            testRunner.on('renderitem', function(){
                start();
            })
            .on('finish', function(){
                stop();
            })
            .on('pause', function(){
                stop();
            })
            .on('resume', function(){
                start();
            });
        },
        render : function render(){

            var $container = this.getAreaBroker().getControlArea();
            $container.append(this.$element);


        },
        destroy : function (){
            this.stop();
        }
    });
});
