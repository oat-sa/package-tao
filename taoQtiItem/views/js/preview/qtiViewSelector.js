define(['jquery'], function($){

    var ViewSelector = {
        exec : function(userVars){
            
            var $list = $('<ul>')
                .append($('<li>', {id : 'qti-view-author', 'class' : 'qti-view-option', 'data-view' : 'author', text : 'author'}))
                .append($('<li>', {id : 'qti-view-candidate', 'class' : 'qti-view-option', 'data-view' : 'candidate', text : 'candidate'}))
                .append($('<li>', {id : 'qti-view-proctor', 'class' : 'qti-view-option', 'data-view' : 'proctor', text : 'proctor'}))
                .append($('<li>', {id : 'qti-view-scorer', 'class' : 'qti-view-option', 'data-view' : 'scorer', text : 'scorer'}))
                .append($('<li>', {id : 'qti-view-testConstructor', 'class' : 'qti-view-option', 'data-view' : 'testConstructor', text : 'test constructor'}))
                .append($('<li>', {id : 'qti-view-tutor', 'class' : 'qti-view-option', 'data-view' : 'tutor', text : 'tutor'}))
                .hide();

            var $selected = $list.find('[data-view=' + userVars.view + ']').switchClass('qti-view-option', 'qti-view-selected');

            var $placeholder = $('<span>', {id : 'qti-preview-view', text : $selected.text()});
            var $options = $('<div>', {id : 'qti-preview-view-options', text : 'view as '}).css({'position' : 'fixed', 'right' : 0, 'top' : 0}).append($placeholder).append($list);

            var startSelectView = function startSelectView(){
                $placeholder.data('selecting', true);
                $list.show();
            }

            var stopSelectView = function stopSelectView(){
                $placeholder.removeData('selecting');
                $list.hide();
            }

            $placeholder.on('click', function(e){
                e.stopPropagation();
                if($(this).data('selecting')){
                    stopSelectView();
                }else{
                    startSelectView();
                }
            });

            $list.find('li.qti-view-option').on('click', function(){
                $placeholder.text($(this).text());
                $placeholder.after($('<span>', {text : '...'}));
                $list.find('li.qti-view-selected').switchClass('qti-view-selected', 'qti-view-option');
                $(this).switchClass('qti-view-option', 'qti-view-selected');
                stopSelectView();
                var href = window.location.href + '&view=' + $(this).data('view');
                href = href.replace('#', '');
                window.location.href = href;
            });

            $('body').append($options).on('click', stopSelectView);
        }
    };

    return ViewSelector;
});