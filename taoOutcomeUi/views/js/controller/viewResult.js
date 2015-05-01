/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'module', 
    'jquery', 
    'i18n', 
    'helpers', 
    'layout/section',
    'taoItems/preview/preview',
    'jquery.fileDownload'
], function (module, $,  __,  helpers, section, preview) {
    'use strict';    
    
    /**
     * @exports taoOutcomeUi/controller/viewResult
     */
    var viewResultController =  {

        /**
         * Controller entry point
         */
        start : function(){
           var conf = module.config();
           var $container = $('#view-result');
           var $filterField = $('.result-filter', $container);              
            //set up filter field
            $filterField.select2({
                minimumResultsForSearch : -1
            }).select2('val', conf.filter || 'all');

            $('.result-filter-btn', $container).click(function(e) {
                section.loadContentBlock(
                    helpers._url('viewResult', 'Results', 'taoOutcomeUi'), {
                    uri: conf.uri,
                    classUri:  conf.classUri,
                    filter: $filterField.select2('val')
                });
            });


            //bind the download buttons
            $('.download', $container).on("click", function (e) {
                var variableUri = $(this).val();
                $.fileDownload(helpers._url('getFile', 'Results', 'taoOutcomeUi'), {
                    preparingMessageHtml: __("We are preparing your report, please wait..."),
                    failMessageHtml: __("There was a problem generating your report, please try again."),
                    httpMethod: "POST",
                    //This gives the current selection of filters (facet based query) and the list of columns selected from the client (the list of columns is not kept on the server side class.taoTable.php
                    data: {'variableUri': variableUri, 'deliveryUri':conf.classUri}
                });
            });

            $('.preview', $container).on("click", function (e) {
                e.preventDefault();
                window.scrollTo(0,0);
                preview.init(helpers._url('forwardMe', 'ItemPreview', 'taoItems', {uri : $(this).data('uri')}));
                preview.show();
            });

        }
    };

    return viewResultController;
});
