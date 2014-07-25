define(['jquery'], function($){
    'use strict'

    var getCurrentEyeCatchers = function(options){

        var tmpCatchers = $('[data-eyecatcher]').filter(':visible'),
            tmpLength = tmpCatchers.length,
            win = $(window),
            winLeft = win.scrollLeft(),
            winTop = win.scrollTop(),
            winHeight = win.height(),
            winWidth = win.width(),
            eyeCatchers = $(),
            visibleCatchers = $(),
            offset,
            left,
            top,
            data;


        // determine the real eye catchers
        tmpCatchers.each(function(){
            var elem = $(this),
                data = elem.data('eyecatcher');
            if(false === data){
                return true;
            }
            else if((data === '') || (data === true)){
                eyeCatchers = eyeCatchers.add(elem);
            }
            else if($.type(data) === 'string'){
                eyeCatchers = eyeCatchers.add(elem.find(data));
            }
        });


        if(!eyeCatchers.length){
            return false;
        }

        // all elements have run once
        if(eyeCatchers.length === tmpLength){
            return null;
        }

        eyeCatchers.each(function(){
            var elem = $(this);
            offset = elem.offset();
            left = offset.left;
            top = offset.top;


            if(top + elem.height() >= winTop
                && top <= winTop + winHeight
                && left + elem.width() >= winLeft
                && left <= winLeft + winWidth){
                visibleCatchers = visibleCatchers.add(elem);
            }
        });

        return visibleCatchers;
    };

    var eyeCatcher = function(){
        var type = 'info',
            eyeCatchers = null,
            i = arguments.length;
        
        while(i--){
            if(typeof(arguments[i]) === 'string' && arguments[i]){
                type = arguments[i];
            }else if(arguments[i] instanceof $){
                eyeCatchers = arguments[i];
            }
        }
        type = type || 'info';

        var check = setInterval(function(){
            
            eyeCatchers = eyeCatchers || getCurrentEyeCatchers();
            
            // all done
            if(null === eyeCatchers){
                clearInterval(check);
                return false;
            }

            // all done for now, but there might be more coming up
            if(false === eyeCatchers){
                return false;
            }

            // there are actual eye catchers available
            eyeCatchers.animate({
                boxShadow : '0 0 4px #3e7da7'
            },
            500,
                function(){
                    // run them only once
                    eyeCatchers.data('eyecatcher', false);
                    setTimeout(function(){
                        eyeCatchers.animate({
                            boxShadow : '0 0 0 0'
                        }, 1000)
                    }, 500);
                });
        }, 1000);

    };
    return eyeCatcher;
});

