define(['jquery'], function($){

    function getSelection(){

        var selection;

        if(window.getSelection){
            selection = window.getSelection();
        }else if(document.selection){
            selection = document.selection.createRange();
        }

        return selection;
    }

    function containElement(selection, range){

        if(range.commonAncestorContainer.nodeType === 1){
            var allWithinRangeParent = range.commonAncestorContainer.getElementsByTagName("*");
            var allSelected = [];
            for(var i = 0, el; el = allWithinRangeParent[i]; i++){
                // The second parameter says to include the element 
                // even if it's not fully selected
                if(selection.containsNode(el, true)){
                    allSelected.push(el);
                    return true;
                }
            }
        }

        return false;
    }

    function wrapSelection(wrap){

        var sel = getSelection();
        if(sel.rangeCount){
            var range = sel.getRangeAt(0).cloneRange();
            if(range.startOffset !== range.endOffset && //prevent empty selection
                range.toString().trim() && //prevent empty selection
                !containElement(sel, range)
                ){
                        
                range.surroundContents(wrap);
                sel.removeAllRanges();
                sel.addRange(range);
                return true;
            }
        }

        return false;
    }

    function unwrapSelection($editable){

        $editable.trigger('beforeunwrap');

        $editable.find('#selection-wrapper').replaceWith(function(){
            return $(this).html();
        });

        $editable.trigger('unwrapped');
    }

    var textWrapper = {
        create : function($editable){

            //reset it first:
            textWrapper.destroy($editable);

            //add listeners:
            $editable.on('mouseup.textwrapper', function(e){

                var $target = $(e.target);
                if($target.hasClass('html-editable-shield') || $target.hasClass('widget-box')){
                    return;
                }

                var $wrapper = $('<span>', {id : 'selection-wrapper', 'class' : 'inline-text-wrapper'});
                if(wrapSelection($wrapper[0])){
                    var wrappedText = $wrapper.text().trim();
                    $editable.trigger('wrapped', [$wrapper, wrappedText]);
                }

            }).on('mousedown.textwrapper', function(){

                unwrapSelection($editable);

            });

        },
        destroy : function($editable){

            unwrapSelection($editable);
            $editable.off('.textwrapper');
        }
    };

    return textWrapper;
});