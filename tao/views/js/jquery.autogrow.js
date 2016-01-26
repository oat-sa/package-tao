define(['jquery'], function($){

    /**
     * Jehan Bihin
     * Autogrow for textarea
     */
    $.fn.autogrow = function(options){
        return this.each(function(){
            var offset = 20;
            var minHeight = $(this).height();
            var maxHeight = 0;
            if(options && options.minHeight){
                minHeight = options.minHeight ? options.minHeight : minHeight;
                maxHeight = options.maxHeight ? options.maxHeight : maxHeight;
            }
            $(this).before('<div style="width: ' + $(this).width() + 'px; display: none;" class="helper"></div>');
            $(this).keyup(function(e){
   
                $(this).prev('.helper').html($(this).val().replace(/\r\n|\r|\n/g, '<br>'));
                var height = $(this).prev().height() + offset;
                if(height < minHeight){
                    height = minHeight;
                }
                if(maxHeight && height > maxHeight){
                    height = maxHeight;
                }
                $(this).height(height + "px");
            }).keyup();
        });
    };

});