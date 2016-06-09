define(['jquery', 'lodash'], function($, _){
    "use strict";
    function create($container, selector, callback){

        function _init($elt){
            $elt.attr('contenteditable', true).addClass('simple-editable');
        }

        _init($container.find(selector));

        $container.on('mouseenter.simpleeditable', selector, _.throttle(function(){

            var $elt = $(this);
            if(!$elt.attr('contenteditable')){
                //incase the element does not exists on init()
                _init($elt);
            }

        }, 200)).on('keyup.simpleeditable', selector, _.throttle(function(e){

            callback($(this).text());

        }, 200)).on('keydown.simpleeditable', selector, function(e){

            if(e.which === 13){
                e.preventDefault();
                $(this).blur();
            }
        });

    }

    function destroy($container){

        $container.find('.simple-editable')
            .removeClass('simple-editable')
            .removeAttr('contenteditable');

        $container.off('.simpleeditable');
    }

    return {
        create : create,
        destroy : destroy
    };

});
