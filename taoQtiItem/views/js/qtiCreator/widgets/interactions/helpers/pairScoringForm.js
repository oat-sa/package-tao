/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 
    'lodash' ,
    'i18n',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/response/pairScoreMappingForm',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/response/pairScoreForm',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'ui/tooltipster',
    'ui/selecter'
], function($, _, __, formTpl, pairTpl, answerStateHelper, formElementHelper, tooltipster, selecter){

    //to bind html element to a pair, we use this separator to replace spaces in the pair name.
    //The colons char isn't allowed by QTI but it is in HTML, so no change to encounter this separator into the pairs id.
    var separator = {
        html : '::',
        qti  : ' '
    };

    /**
     * Create a new instance of PairScoringForm
     * @exports taoQtiItem/qtiCreator/widgets/interactions/helpers/pairScoringForm
     * @param {Object} widget - the interaction widget 
     * @param {Object} [options]
     * @returns {PairScoringForm} the form compoenent, loaded by default.
     */
    return function formFactory(widget, options){

        var $container  = widget.$container;
        var interaction = widget.element;
        var response    = interaction.getResponseDeclaration();
        var $popup      = [];
        var pairs       = [];
        var $form;

        /**
         * The form compoenent
         * @typedef PairScoringForm
         */
        var pairScoringForm = {

            /**
             * Load / reload the compoenent
             * @memberof PairScoringForm
             * @returns {PairScoringForm} for chaining
             */
            load : function load(){
                var self            = this;
                var callbacks       = {};
                var corrects        = _.values(response.getCorrect());
                var mapEntries      = response.getMapEntries();
           
                //create / update the HTML element
                if(!$popup.length){
                    $popup = createPopup($container);
                } else {
                    $popup.empty();
                }
                
                if(options){

                    //prepare content for the form, using either current map entries or data given in options.
                    if(options.entries){
                        pairs = preparePairs(options.entries);

                        //update the current map entries according to what we got in entry
                        response.removeMapEntries();
                        _.forEach(pairs , function(pair){
                            if(typeof pair.score === 'undefined' || pair.score === null){
                                pair.score = response.mappingAttributes.defaultValue || 0;
                            }
                            response.setMapEntry(pair.id.replace(separator.html, separator.qti), pair.score);
                        });
                    } else {
                        pairs = preparePairs(mapEntries);
                    }

                    //run the template with the data
                    $form = $(formTpl({
                        'title'             : options.title      || __('Pair scoring'),
                        'leftTitle'         : options.leftTitle  || __('Choices'),
                        'rightTitle'        : options.rightTitle || __('Gaps'),
                        'defineCorrect'     : answerStateHelper.defineCorrect(response),
                        'scoreMin'          : response.getMappingAttribute('lowerBound'),
                        'scoreMax'          : response.getMappingAttribute('upperBound'),
                        'pairs'             : _.map(pairs, pairTpl),
                        'pairLeft'          : _.isFunction(options.pairLeft) ? options.pairLeft() : _.values(interaction.getChoices()), 
                        'pairRight'         : _.isFunction(options.pairRight) ? options.pairRight() : _.values(interaction.getChoices()) 
                    }));
                    
                    updateFormBindings();

                    $popup.append($form);

                    //initialize UI componenets manually 
                    selecter($popup);
                    tooltipster($popup);
                    deleter($popup);
                    adder($popup);
                }

                return this;
            },

            /**
             * Add a new pair
             * @memberof PairScoringForm
             * @param {String} key - the pair key
             * @returns {PairScoringForm} for chaining
             */
            addPair : function addPair(key){
                
                var score = response.mappingAttributes.defaultValue;
                var pair  = formatPair(score, key, true);
                
                //update internal model
                pairs.push(pair);

                //update the response
                response.setMapEntry(key, score);

                $('.pairs', $popup).append(pairTpl(pair));

                updateFormBindings();
               
                if(_.isFunction(options.add)){
                    options.add(key);
                }

                return this;
            },

            /**
             * Remove a pair
             * @memberof PairScoringForm
             * @param {String} key - the pair key to remove
             * @returns {PairScoringForm} for chaining
             */
            removePair : function removePair(key){

                //update internal model
                _.remove(pairs, {id : key.replace(separator.qti, separator.html)});

                //update the response
                response.removeMapEntry(key);
   
                if(_.isFunction(options.remove)){
                    options.remove(key);
                }
        
                return this;
            },

            /**
             * Destroy the compoenent
             * @memberof PairScoringForm
             */
            destroy : function(){
                if($popup.length){
                    $popup.remove();
                }
                
                //reset overflow
                $('#item-editor-panel').css('overflow', '');
            }
        };

        /**
         * Set up the pair adder. 
         * Disable list options according to the current values. 
         * 
         * @private
         * @param {jQueryElement} $container - to scope element finding
         */
        function adder($container){
           var $panel   = $('.panel-new-pair', $container); 
           var $adder   = $('.pair-adder', $container);
           var $left    = $('.new-pair-left', $container);
           var $right   = $('.new-pair-right', $container);
           var $options = $('.select2 > option', $container); 

           //disable options based on existing pairs
           $left.on('change', function(){
                var currentLeft = $left.select2('val'),
                    currentRight = $right.select2('val');
                
                $options.removeProp('disabled'); 
                
                _(pairs).where({leftId : currentLeft}).forEach(function(pair){
                    $right.find('option[value="' + pair.rightId+ '"]').prop('disabled', true);
                });
                _(pairs).where({rightId : currentRight}).forEach(function(pair){
                    $left.find('option[value="' + pair.leftId+ '"]').prop('disabled', true);
                });
                if(options.type === 'pair'){
                    _(pairs).where({rightId : currentLeft}).forEach(function(pair){
                        $right.find('option[value="' + pair.leftId+ '"]').prop('disabled', true); 
                    });
                }
           });
           $right.on('change', function(){
                var currentRight = $right.select2('val'),
                    currentLeft = $left.select2('val');
                
                $options.removeProp('disabled'); 
                
                _(pairs).where({rightId : currentRight}).forEach(function(pair){
                    $left.find('option[value="' + pair.leftId+ '"]').prop('disabled', true);
                });
                _(pairs).where({leftId : currentLeft}).forEach(function(pair){
                    $right.find('option[value="' + pair.rightId+ '"]').prop('disabled', true);
                });
                if(options.type === 'pair'){
                    _(pairs).where({leftId : currentRight}).forEach(function(pair){
                        $left.find('option[value="' + pair.rightId+ '"]').prop('disabled', true); 
                    });
                }
           });

           //create a new pair
           $adder.on('click', function(e){
               $options.removeProp('disabled');
               e.preventDefault();
               var key;
               var lval = $left.select2('val');
               var rval = $right.select2('val');
               if(lval && rval){
                   //update the form component
                   key      = lval + separator.qti + rval;
                   pairScoringForm.addPair(key);

                   //reset the select lists
                   $left.select2('val', '');
                   $right.select2('val', '');
                }
           }); 
        }

        /**
         * Set up the pair remover.
         * @private
         * @param {jQueryElement} $container - to scope element finding
         */
        function deleter($container){
            $container
              .off('click', '.pair-deleter')
              .on('click', '.pair-deleter', function(e){
                e.preventDefault();

                var $elt = $(this);
                var key = $elt.attr('id')
                              .replace(/-delete$/, '')
                              .replace(separator.html, separator.qti);
    
                pairScoringForm.removePair(key);
                
                $elt.closest('.grid-row').remove();
           });
        }

        /**
         * Transform entries to pair object that contains the property required by the template.
         * @private
         * @param {Array} entries - using the response.mapEntries format pair:score, ie.  <pre>{ "choice1 choice2" : 3  }</pre>
         * @returns {Array} the pairs as a collection of objects.
         */
        function preparePairs(entries){
            var defaultScore = parseFloat(response.mappingAttributes.defaultValue);
            return _.map(entries, function(value, key){
                return formatPair(value, key, parseFloat(value) === defaultScore);
            });
        }


        /**
         * Format a key/score mapping to a pair object used by the template and by this scoring component.
         * @private
         * @param {Number} score - the value mapped to the pair
         * @param {String} key - the pair key
         * @param {Boolean} [default = false] - is the score the default value
         * @returns {Object} the pair 
         */
        function formatPair(score, key, defaultScore){
            var pair = key.split(separator.qti);
            return {
                id              : key.replace(separator.qti, separator.html),
                score           : score,
                defaultScore    : !!defaultScore, 
                leftId          : pair[0],
                rightId         : pair[1],
                left            : _.isFunction(options.formatLeft) ? options.formatLeft(pair[0]) : pair[0],
                right           : _.isFunction(options.formatRight) ? options.formatRight(pair[1]) : pair[1],
                defineCorrect   : answerStateHelper.defineCorrect(response),
                correct         : _.contains(_.values(response.getCorrect()), key) 
            };
        }

        /**
         * Update the form bindings callbacks.
         * @private
         */
        function updateFormBindings(){
            var callbacks = {};
            var corrects  = _.values(response.getCorrect());
    
            if($form.length){

               //the default value changes
                widget.on('mappingAttributeChange', function(data){
                    if(data.key === 'defaultValue'){
                        _.forEach(pairs, function(pair){
                            var $score = $('[name="' +  pair.id + '-score"]', $form);
                            if($score.data('default')){
                                $score.val(data.value);
                                response.setMapEntry( pair.id.replace(separator.html, separator.qti), data.value);
                            } 
                        });
                    }
                });

                //creates callbacls for score and correct, for each pair
                _.forEach(pairs, function(pair){
                    var id = pair.id.replace(separator.html, separator.qti);
                    callbacks[pair.id + '-score'] = function(response, value){
                        $('[name="' +  pair.id + '-score"]', $form).removeAttr('data-default').removeData('default');
                        response.setMapEntry(id, value);
                    };
                    callbacks[pair.id + '-correct'] = function(response, value){
                        if(value === true){
                            if(!_.contains(corrects, id)){
                                corrects.push(id);
                            }
                        } else {
                            corrects = _.without(corrects, id);
                        }
                        response.setCorrect(corrects);
                    };
                });
                
                //set up the form data binding
                formElementHelper.setChangeCallbacks($form, response, callbacks);
            }
        }
        
        return pairScoringForm.load();
    };

    /**
     * Creates a popup relative to container
     * @private
     * @returns {jQueryElement} the popup
     */
    function createPopup($container){
        var $element    = $('<div class="mapping-editor arrow-top-left"></div>'); 
        var width       = $container.innerWidth();
        var height      = $container.innerHeight();

        //only one 
        $('.mapping-editor', $container).remove();

        //style and attach the form
        $element.css({       
            'top'       : height - 30,
            'width'     : width - 100
        }).appendTo($container);

        return $element;
    }
});
