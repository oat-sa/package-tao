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
        name : 'nextButton',
        init : function init(){
            var self = this;
            var testRunner = this.getTestRunner();

            this.$button = $('<button class="next"> Next &gt;&gt; </button>');

            this.$button.click(function(){
                testRunner.next();
            });

            testRunner.on('renderitem', function(){
                var context = this.getTestContext();
                if(context.current === context.items.length){
                    self.disable();
                } else {
                    self.enable();
                }
            })
            .on('finish', function(){
                self.disable();
            })
            .on('pause', function(){
                self.disable();
            }).on('resume', function(){
                self.enable();
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
