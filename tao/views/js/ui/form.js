define(['jquery'], function($){
    'use strict';

    /**
     * Toggle radios and checkboxes wrapped into a pseudo label element to simulate the behavior of a label
     * @param {String} selector - to scope the listening
     */
    var pseudoLabel = function pseudoLabel(selector){

        $(document).on('click', selector + ' .pseudo-label-box', function (e) {
            e.preventDefault();

            var $box = $(this);
            var $radios =  $box.find('input:radio').not('[disabled]').not('.disabled');
            var $checkboxes = $box.find('input:checkbox').not('[disabled]').not('.disabled');
           
            if($radios.length){
                $radios.not(':checked').prop('checked', true);
                $radios.trigger('change');
            }
            if($checkboxes.length){
               $checkboxes.prop('checked', !$checkboxes.prop('checked')); 
               $checkboxes.trigger('change');
            }
        });
    };

    /**
     * Prevent clicks and focus on disbled elements
     * @param {String} selector - to scope the listening
     */
    var preventDisabled = function preventDisabled(selector){
        
        $(document).on('click', selector + ' .disabled, ' + selector + ' :disabled', function (e) {
            e.preventDefault();
            return false;
        });
    };

   /**
    * Manages general behavior on form elements
    * 
    * @param {jQueryElement} $container - the root context to lookup inside
    */
    return function listenFormBehavior($container){
        var selector = $container.selector || '.tao-scope'; 

        pseudoLabel(selector);
        preventDisabled(selector);
    };
});
