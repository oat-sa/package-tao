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
define([
    'lodash',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/modalFeedback',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiItem/helper/container',
    'ui/waitForMedia',
    'ui/modal'
], function(_, tpl, containerHelper, coreContainerHelper){
    'use strict';

    var modalFeedbackRenderer = {
        qtiClass : 'modalFeedback',
        template : tpl,
        getContainer : containerHelper.get,
        minHeight : 200,
        width : 600,
        getData : function(fb, data){
            var feedbackStyle = coreContainerHelper.getEncodedData(fb, 'modalFeedback');
            data.feedbackStyle = feedbackStyle;
            return data;
        },
        render : function(modalFeedback, data){

            data = data || {};

            var $modal = containerHelper.get(modalFeedback);

            $modal.waitForMedia(function(){

                //when we are sure that media is loaded:
                $modal.on('opened.modal', function(){

                    //set item body height
                    var $itemBody = containerHelper.get(modalFeedback.getRelatedItem()).children('.qti-itemBody');
                    var requiredHeight = $modal.outerHeight() + parseInt($modal.css('top'));
                    if(requiredHeight > $itemBody.height()){
                        $itemBody.height(requiredHeight);
                    }

                }).on('closed.modal', function(){
                    if(_.isFunction(data.callback)){
                        data.callback.call(this);
                    }
                }).modal({
                    startClosed : false,
                    minHeight : modalFeedbackRenderer.minHeight,
                    width : modalFeedbackRenderer.width
                });
            });

        }
    };

    return modalFeedbackRenderer;
});
