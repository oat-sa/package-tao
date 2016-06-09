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
 * Test Runner Control Plugin : Title
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'taoTests/runner/plugin',
    'tpl!taoQtiTest/runner/plugins/controls/title/title'
], function ($, __, pluginFactory, titleTpl){
    'use strict';

    return pluginFactory({
        name : 'title',
        init : function init(){
            var self = this;
            var testRunner = this.getTestRunner();
            var testData   = testRunner.getTestData();

            var createElement = function(){

                var context  = testRunner.getTestContext();
                var titles =  [{
                    control : 'qti-test-title',
                    text    : testData.title
                }];
               if(context.isDeepestSectionVisible){
                    titles.push({
                        control : 'qti-test-position',  //WTF !? isn't it the section title... ?
                        text    : context.sectionTitle
                    });
                }

                return $(titleTpl({ titles : titles }));
            };

            this.$element = createElement();


            testRunner
                .after('renderitem', function(){
                    var $element = createElement();
                    self.$element.replaceWith($element);
                    self.$element = $element;
                });

        },
        render : function render(){
            var $container = this.getAreaBroker().getControlArea();
            $container.append(this.$element);
        }
    });
});
