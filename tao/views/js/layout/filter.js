define([
'jquery',
'lodash'
], function($, _) {
    'use strict';

    /**
     * Initialize the filter form : make a click on the button to filter the tree
     * @private
     * @param {jQueryElement} $field - the input field used to filter
     * @fires layout/tree#refresh.taotree
     */
    function initFiltering($field){
        var lastValue; 
        var filterHandler = _.debounce(function filterHandler(e){
            
            var value = $field.val();
            if(value.length > 3 || (!value && lastValue.length) || e.which === 13){
                //ask the tree to refresh 
                $('.tree').trigger('refresh.taotree', [{ 
                    filter      : value || '*'
                }]);
                lastValue = value;
            }
        }, 300);

        $field.on('keypress', filterHandler)
              .focus();
    }

    /**
     * Reset the filtering form
     * @private
     * @param {jQueryElement} $field - the input field used to filter
     * @fires layout/tree#refresh.taotree
     */
    function resetFiltering($field){

        //empty the field
        $field.val('');

        //reset the trees 
        $('.tree').trigger('refresh.taotree', [{ filter : '*' }]);
    }

    /**
     * This component helps you to filter the tree.
     * 
     * @exports layout/filter
     * 
     * @param {jQueryElement} $container - to scope the queries
     */
    return function toggleFilter($container){

       var $filterField = $(':text', $container); 

       //initFiltering($filterField); 
       if($container.is(':visible')){    
            resetFiltering($filterField);
            $container.hide();
       } else {
            $container.show();
            initFiltering($filterField); 
        }
    };
});
