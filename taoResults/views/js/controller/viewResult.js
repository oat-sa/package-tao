define(['module', 'jquery', 'context', 'i18n', 'helpers', 'jquery.fileDownload'], function (module, $, context, __,  helpers) {
        
       return {

            start : function(){
                
               var conf = module.config();
               
                $('#filter').val(conf.filter);
              
                $('.dataResult').html(function(index, oldhtml) {
                    return oldhtml;
                });
                
                $('#btnFilter').click(function(e) {
                    var url = context.root_url + 'taoResults/Results/viewResult';
                    //conf.filter = $( this ).val();
                    helpers._load(helpers.getMainContainerSelector(), url, {
                    uri: conf.uri,
                    classUri:  conf.classUri,
                    filter: $('#filter').val()
                     });
                });


                $('.download').on("click", function (e) {
                    var variableUri = $(this).val();
                    $.fileDownload(context.root_url + 'taoResults/Results/getFile', {
                      preparingMessageHtml: __("We are preparing your report, please wait..."),
                      failMessageHtml: __("There was a problem generating your report, please try again."),
                      successCallback: function () { },
                      httpMethod: "POST",
                       ////This gives the current selection of filters (facet based query) and the list of columns selected from the client (the list of columns is not kept on the server side class.taoTable.php
                      data: {'variableUri': variableUri}
                    });
                });
            }
    };
});
