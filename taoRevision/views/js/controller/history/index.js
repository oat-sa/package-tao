define([
    'jquery',
    'i18n',
    'tpl!taoRevision/controller/history/line',
    'helpers',
    'ui/feedback',
    'select2',
    'tooltipster'
    ], function($, __, lineTpl, helpers, feedback){
        'use strict';

        /**
         * Restore a revision
         * @param  {DOM Element} element DOM element that triggered the function
         */
        var _restoreRevision = function(element) {
            var $table = $('#revisions-table'),
                body = $table.find('tbody')[0];

            //Get the revision id
            var $this = $(element),
                revision = $this.data('revision');
            var id = $('#resource_id').val();

            var message = prompt("Please enter a message", __("Restored version %s", revision));

            if( typeof revision !== "undefined" &&
                typeof message !== "undefined" &&
                message != null &&
                revision !== "" &&
                message !== ""){
                $.post(
                    helpers._url('restoreRevision', 'History', 'taoRevision'),
                    {id : id, revisionId : revision, message : message})
                    .done(function(res){
                        if(res && res.success){
                            feedback().success(__("Resource restored"));
                            $(body).append(lineTpl(res));
                            $('.tree').trigger('refresh.taotree'); 
                        } else {
                            feedback().error(__("Something went wrong..."));
                        }
                    })
            }
        };

        var mainCtrl = {
            'start' : function(){

                var $container = $('.revision-container'),
                    $form      = $('form', $container),
                    $submiter  = $(':submit', $form);

                var $table = $('#revisions-table'),
                    body = $table.find('tbody')[0];

                $form.on('submit', function(e){
                    e.preventDefault();
                    e.stopImmediatePropagation();
                });
                $submiter.on('click', function(e){
                    e.preventDefault();

                var $message = $('#message',$form);
                if($message.val() !== ""){
                    $submiter.addClass('disabled');
                    $.post($form.attr('action'), $form.serialize())
                        .done(function(res){
                            if(res && res.success){
                                feedback().success(res.commitMessage);
                                $(body).append(lineTpl(res));


                            } else {
                                feedback().error(__("Something went wrong..."));
                            }
                        })
                        .complete(function(){
                            $submiter.removeClass('disabled');
                            $message.val('');
                        });
                }
                else{
                    feedback().error(__("Please give a message to your commit"));
                }
                });


                $table.on('click', '.restore_revision', function(event) {
                    event.preventDefault();
                    _restoreRevision(this);
                });
            }
        };

        return mainCtrl;
    })
