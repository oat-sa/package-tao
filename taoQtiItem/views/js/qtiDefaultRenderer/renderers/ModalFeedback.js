define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/modalFeedback'], function(tpl){
    return {
        qtiClass : 'modalFeedback',
        template : tpl,
        render : function(modalFeedback, data){
            $('#' + modalFeedback.getSerial()).dialog({
                modal : true,
                width : 400,
                height : 300,
                buttons : [
                    {
                        text : 'Close',
                        click : function(){
                            $(this).dialog('close');
                        }
                    }
                ],
                open : function(){
                    modalFeedback.getBody().postRender(modalFeedback.getRenderer());
                },
                close : function(){
                    $(this).empty();
                    if(typeof(data.callback) === 'function'){
                        data.callback();
                    }
                }
            });
        }
    };
});