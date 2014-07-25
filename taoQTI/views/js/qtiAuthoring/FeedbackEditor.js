function FeedbackEditor(feedbackSerial){

    this.serial = feedbackSerial;
    var feedbackEditor = this;

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/editModalFeedback",
        dataType : 'html',
        data : {
            feedbackSerial : feedbackSerial
        },
        success : function(html){

            $('<div id="modalFeedbackForm" title="' + __('Feedback Editor') + '">' + html + '</div>').dialog({
                modal : true,
                width : 600,
                height : 680,
                buttons : [
                    {
                        text : __('Save'),
                        click : function(){
                            feedbackEditor.save();
                        }
                    },
                    {
                        text : __('Save & Close'),
                        click : function(){
                            var $modal = $(this);
                            feedbackEditor.save(function(){
                                $modal.dialog('close');
                            });
                        }
                    },
                    {
                        text : __('Close'),
                        click : function(){
                            $(this).dialog('close');
                        }
                    }
                ],
                open : function(){

                    feedbackEditor.$form = $(this);
                    var htmlEditor = new HtmlEditor('feedback', feedbackSerial, feedbackEditor.$form.find('#data'));
                    htmlEditor.iFrameClass = 'wysiwyg-feedback';
                    htmlEditor.initEditor();

                }
            });

        }
    });

}

FeedbackEditor.prototype.save = function(callback){

    var feedbackEditor = this;

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/saveModalFeedback",
        dataType : 'json',
        data : {
            feedbackSerial : this.serial,
            title : this.$form.find('input#title').val(),
            data : this.$form.find('textarea#data').val()
        },
        success : function(data){
            if(data.saved){
                feedbackEditor.$form.find('div#formModalFeedback_errors').text('').hide();
                if(typeof(callback) === 'function'){
                    callback();
                }
            }else{
                feedbackEditor.$form.find('div#formModalFeedback_errors').text(data.errorMessage).show();
            }
        }
    });
}