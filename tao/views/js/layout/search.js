define([
    'jquery',
    'lodash',
    'i18n',
    'module',
    'context',
    'layout/section',
    'ui/feedback',
    'ui/datatable',
    'uri'
],
function($, _, __, module, context, section, feedback, datatable, uri){

    var changeFormLayout = function changeFormLayout($form){

        var $toolBars      = $form.find('.form-toolbar');
        var $formGroups    = $form.find('.form-group');
        var $filters       = $formGroups.last();
        var $langSelector  = $form.find('[name="lang"]');
        var $formContainer = $form.find('.xhtml_form');
        var $formTitle     = $form.find('h2');

        // remove unwanted classes
        $formContainer.parent().removeClass(function(idx, className) {
            return className;
        });

        // remove first toolbar
        if($toolBars.length > 1) {
            $toolBars.first().remove();
        }

        // remove 'options', 'filters' and headings
        $form.find('del').remove();
        $formTitle.remove();

        // select current locale
        if(!$langSelector.val()){
            $langSelector.val(context.locale);
        }

        // add regular placeholder
        $filters.find('input[type="text"]').each(function() {
            var $parentDiv;
            if((/schema_[\d]+_label$/).test(this.name)) {
                this.placeholder = __('You can use * as a wildcard');
                $parentDiv = $(this).closest('div');
                // remove 'original filename when empty
                if(!$.trim($parentDiv.next().find('span').last().html())) {
                    $parentDiv.next().remove();
                }
                $parentDiv.prependTo($form.find('.form-group:first > div'));
            }
        });
    };

    var buildResponseTable  = function buildResponseTable(data){
        var $tableContainer = $('<div class="flex-container-full"></div>');
        section.updateContentBlock($tableContainer);

        $tableContainer.datatable({
                'url': data.url,
                'model' : _.values(data.model),
                'actions' : {
                   'open' : function openResource(id){
                            $('.tree').trigger('refresh.taotree', [{loadNode : id}]);
                    } 
                },
                'params' : {
                    'params' : data.params,
                    'filters': data.filters
                 } 
            });
    };

    return {

        //if in this context the search has been submited at least once
        _submited : false,

        /**
         * Initialize post renderer
         * @param {jQueryElement} $container - the search container
         * @param {String} searchForm - html as a string of the search form
         */
        init : function init($container, searchForm){
            var self = this;
            var conf = module.config();
            var $searchForm;
            var $formElt;
            var submitHandler = function submitHandler(e){
                e.preventDefault();
                e.stopImmediatePropagation();
                
                $.ajax({
                    url : $formElt.attr('action'),
                    type : 'POST',
                    data : $formElt.serializeArray(),
                    dataType : 'json'
                }).done(function(response){
                    if(response.result && response.result === true){
                        buildResponseTable(response);
                        self._submited = true;
                    } else {
                        feedback().warning(__('No results found'));
                    }
                }); 
            };
            if(searchForm){        
    
                // build jquery obj, make ids unique
                $searchForm = $(searchForm.replace(/(for|id)=("|')/g, '$1=$2search_field_'));
                $formElt = $('form', $searchForm);

                //tweaks form layout 
                changeFormLayout($searchForm);

                //re-bind form
                _.defer(function(){     //defer tp bind after the uiForm stuffs
                    $('.form-submitter', $searchForm).off('click').on('click', submitHandler);
                    $formElt.off('submit').on('submit', submitHandler);
                });
                $container.html($searchForm);
            }
        },

        /**
         * Reset the searching
         */
        reset : function reset(){

            if(this._submited){
                this._submited = false;

                //reset the trees 
                $('.tree').trigger('refresh.taotree');
            }
        }
    };
});
