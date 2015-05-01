define(['lodash', 'jquery', 'tpl!taoQtiItem/qtiCreator/tpl/notifications/deletingInfoBox'], function(_, $, deletingInfoTpl){

    var _timeout = 10000;
    
    var _bindEvents = function($messageBox){

        var timeout = setTimeout(function(){
            _confirmDeletion($messageBox, 1000);
        }, _timeout);

        $('body').on('mousedown.deleting keydown.deleting', function(e){
            //confirm deleting whenever user interact with another object
            if(e.target !== $messageBox[0] && !$.contains($messageBox[0], e.target)){
                _confirmDeletion($messageBox, 400);
            }
        });
        
        $messageBox.find('a.undo').on('click', function(e){
            e.preventDefault();
            $messageBox.trigger('undo.deleting');
            clearTimeout(timeout);
            $messageBox.remove();
        });

        $messageBox.find('.close-trigger').on('click', function(e){
            e.preventDefault();
            _confirmDeletion($messageBox, 0);
        });
    };

    var _confirmDeletion = function($messageBox, fadeDelay){
        
        $messageBox.trigger('confirm.deleting');
        $messageBox.fadeOut(fadeDelay, function(){
            $(this).remove();
        });
    };
    
    var deletingHelper = {
        createInfoBox : function(widgets){

            var $messageBox = $(deletingInfoTpl({
                serial : 'widgets',
                count : widgets.length
            }));

            $('body').append($messageBox);

            $messageBox.css({
                'display' : 'block',
                'position' : 'fixed',
                'top' : '50px',
                'left' : '50%',
                'margin-left' : '-200px',
                'width' : '400px',
                zIndex : 999999
            });
            
            _bindEvents($messageBox);
            
            _.each(widgets, function(w){
                w.on('beforeStateInit', function(){
                    _confirmDeletion($messageBox, 400);
                });
            });
        
            return $messageBox;
        }
    };

    return deletingHelper;
});