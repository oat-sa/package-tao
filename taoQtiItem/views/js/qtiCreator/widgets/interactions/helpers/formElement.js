define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiItem/core/Element',
    'tooltipster'
], function($, _, formElement, Element){

    var _scoreTooltipContent = {
        required : 'this is required',
        invalid : 'the score format is not numeric'
    };

    var formElementHelper = {
        init : function(widget){
            formElement.initWidget(widget.$form);
        },

        /**
         * Helps you to synchrnonize min/max widgets so the min isn't greater than the max, etc.
         * @param {Object} widget - the interacion's widget (where widget.element is the interaction)
         * @param {String} [attributeNameMin = minChoices] - the name of the min field and attribute
         * @param {String} [attributeNameMax = maxChoices] - the name of the max field and attribute
         * @param {Function} [getMax = _.size] - how to get the max value from the choices lists (in attributes)
         */
        syncMaxChoices : function(widget, attributeNameMin, attributeNameMax, getMax){

            attributeNameMin = attributeNameMin || 'minChoices';
            attributeNameMax = attributeNameMax || 'maxChoices';
            getMax = getMax || _.size;
            var $min = widget.$form.find('input[name=' + attributeNameMin + ']'),
                $max = widget.$form.find('input[name=' + attributeNameMax + ']');

            var _syncMaxChoices = function(){
                var newOptions = {max : getMax(widget.element.getChoices())};
                $min.incrementer('options', newOptions).keyup();
                $max.incrementer('options', newOptions).keyup();
            };

            widget.on('choiceCreated', function(data){

                if(data.interaction.serial === widget.element.serial){
                    _syncMaxChoices();
                }

            }).on('deleted', function(data){

                if(data.parent.serial === widget.element.serial &&
                    Element.isA(data.element, 'choice')){

                    _syncMaxChoices();
                }
            });

        },

        //set float (used for score)
        setScore : function($scoreInput, options){

            options = _.defaults(options || {}, {
                required : false,
                empty : function(){
                },
                set : function(){
                },
                key : function(){
                    return $(this).attr('name');
                },
                tooltipContent : _scoreTooltipContent
            });

            if(!$scoreInput.hasClass('tooltipstered')){
                $scoreInput.tooltipster({
                    theme : 'tao-error-tooltip',
                    content : options.tooltipContent.invalid,
                    delay : 350,
                    trigger : 'custom'
                });
            }

            var value = $scoreInput.val(),
                score = parseFloat(value),
                key = options.key.call($scoreInput[0]);

            if(value === ''){
                if(options.required){
                    //missing required score value!
                    $scoreInput.tooltipster('content', options.tooltipContent.required);
                    $scoreInput.tooltipster('show');
                }else{
                    $scoreInput.tooltipster('hide');
                    options.empty(key);
                }
            }else if(!isNaN(score)){
                //is a correct number
                $scoreInput.tooltipster('hide');
                options.set(key, score);
            }else{
                //invalid input!
                $scoreInput.tooltipster('content', options.tooltipContent.invalid);
                $scoreInput.tooltipster('show');
            }

        }
        //set text (used for controlled pattern, especially id)
    };

    return formElementHelper;
});
