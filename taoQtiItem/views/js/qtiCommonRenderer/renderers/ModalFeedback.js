define([
    'jquery',
    'lodash',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/modalFeedback',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/sizeFinder',
    'ui/modal'
], function($, _, tpl, Helper, sizeFinder){
    'use strict';

    var modalFeedbackRenderer = {
        qtiClass : 'modalFeedback',
        template : tpl,
        getContainer : Helper.getContainer,
        minWidth : 400,
        maxWidth : 800,
        render : function(modalFeedback, data){

            data = data || {};

            var $modal = $('#' + modalFeedback.getSerial());

            sizeFinder.measure($modal, function(size){
                $modal.modal({
                    startClosed : false, 
                    width : Math.max( Math.min(size.width, modalFeedbackRenderer.maxWidth), modalFeedbackRenderer.minWidth)
                });

                $modal.on('closed.modal', function(){
                    if(_.isFunction(data.callback)){
                        data.callback.call(this);
                    }
                });
            });
        }
    };

    return modalFeedbackRenderer;
});
