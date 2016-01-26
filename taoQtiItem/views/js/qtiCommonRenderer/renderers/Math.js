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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * Math common renderer
 *
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/promise',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/math',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'mathJax'
], function(_, Promise, tpl, containerHelper, MathJax){
    'use strict';

    return {
        qtiClass : 'math',
        template : tpl,
        getContainer : containerHelper.get,
        render : function render (math, data){
            return new Promise(function(resolve, reject){
                if(typeof(MathJax) !== 'undefined' && MathJax){
                    //MathJax needs to be exported globally to integrate with tools like TTS, it's weird...
                    if(!window.MathJax){
                        window.MathJax = MathJax;
                    }

                    _.defer(function(){ //defer execution fix some rendering issue in chrome
                        MathJax.Hub.Queue(["Typeset", MathJax.Hub, containerHelper.get(math).parent()[0]]);

                        //@see http://mathjax.readthedocs.org/en/latest/typeset.html
                        MathJax.Hub.Queue(resolve);
                    });
                }
                else {
                    resolve();
                }
            });
        }
    };
});
