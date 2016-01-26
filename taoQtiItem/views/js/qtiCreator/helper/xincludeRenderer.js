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
    'lodash',
    'taoQtiItem/qtiCreator/helper/commonRenderer',
    'taoQtiItem/qtiItem/helper/xincludeLoader'
], function(_, commonRenderer, xincludeLoader){
    'use strict';

    return {
        /**
         * Render (or re-render) the xinclude widget based on the current href and givenBaseUrl
         *
         * @param {Object} xincludeWidget
         * @param {String} baseUrl
         * @returns {undefined}
         */
        render : function render(xincludeWidget, baseUrl, newHref){

            var xinclude = xincludeWidget.element;
            if(newHref){
                xinclude.attr('href', newHref);
            }

            xincludeLoader.load(xinclude, baseUrl, function(xi, data, loadedClasses){
                if(data){
                    //loading success :
                    commonRenderer.get().load(function(){

                        //set commonRenderer to the composing elements only (because xinclude is "read-only")
                        _.each(xinclude.getComposingElements(), function(elt){
                            elt.setRenderer(commonRenderer.get());
                        });

                        //reload the wiget to rfresh the rendering with the new href
                        xincludeWidget.refresh();

                    }, loadedClasses);
                }else{
                    //loading failure :
                    xinclude.removeAttr('href');
                }
            });
        }
    };
});
