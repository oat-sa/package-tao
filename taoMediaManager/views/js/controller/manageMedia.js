/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 
    'i18n', 
    'module',
    'helpers', 
    'layout/actions/binder',
    'uri',
    'ui/previewer'
], function($, __, module, helpers, binder, uri) {
    'use strict';

    var manageMediaController =  {

        /**
         * Controller entry point
         */
        start : function(){

            var $previewer = $('.previewer');
            var file = {};
            file.url = $previewer.data('url');
            file.mime = $previewer.data('type');

            if(!$previewer.data('xml')){
                $previewer.previewer(file);
            }
            else{
                $.ajax({
                    url: file.url,
                    data: {xml:true},
                    method: "POST",
                }).success(function(response){
                    file.xml = response;
                    $previewer.previewer(file);
                });
            }

            $('#edit-media').off()
                .on('click', function(){
                    var action = {binding : "load", url: helpers._url('editMedia', 'MediaImport', 'taoMediaManager')};
                    binder.exec(action, {classUri : $(this).data('classuri'), id : $(this).data('uri')} || this._resourceContext);
                });
        }
    };

    return manageMediaController;
});
