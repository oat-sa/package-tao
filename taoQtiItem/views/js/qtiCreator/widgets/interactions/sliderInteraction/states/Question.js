define([
'taoQtiItem/qtiCreator/widgets/states/factory',
'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
'taoQtiItem/qtiCreator/widgets/helpers/formElement',
'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/slider'
], function(stateFactory, Question, formElement, formTpl){
    var SliderInteractionStateQuestion = stateFactory.extend(Question);
    SliderInteractionStateQuestion.prototype.initForm = function(){
        var _widget = this.widget,
        $form = _widget.$form,
        interaction = _widget.element;

        $form.html(formTpl({
            // tpl data for the interaction
            lowerBound : parseFloat(interaction.attr('lowerBound')),
            upperBound : parseFloat(interaction.attr('upperBound')),
            orientation : interaction.attr('orientation'),
            reverse : !!interaction.attr('reverse'),
            step : parseInt(interaction.attr('step')),
            stepLabel : !!interaction.attr('stepLabel')
        }));
        
        formElement.initWidget($form);
        //  init data change callbacks
        var callbacks = {};
        
        // -- lowerBound Callback
        callbacks.lowerBound = function(interaction, attrValue, attrName){
            
            interaction.attr('lowerBound', parseFloat(attrValue));
            
            var lowerBound = interaction.attr('lowerBound');
            var upperBound = interaction.attr('upperBound');
            var sliderLength = upperBound-lowerBound; //the lenght of the slider
            var step = interaction.attr('step');
            var reverse = !!interaction.attr('reverse');

            //if min is greater than max change upperBound to be like lowerBound
            if(lowerBound>upperBound){
                callbacks.upperBound(interaction, lowerBound);
                _widget.$form.find('input[name="upperBound"]').val(lowerBound);
            }
            
            //check if the lendth of the slide is smaller than the step
            if(sliderLength<step && sliderLength>=0){
                callbacks.step(interaction, sliderLength);
                _widget.$form.find('input[name="step"]').val(sliderLength);
            }
            _widget.$container.find((!reverse) ? '.slider-min' : '.slider-max').text(attrValue);
            _widget.$container.find('span.qti-slider-cur-value').text(lowerBound);
            _widget.$container.find('.qti-slider').noUiSlider({ range: { 'min': lowerBound, 'max': upperBound }}, true);
            _widget.$container.find('.qti-slider').val(lowerBound);
        };
        
        // -- upperBound Callback
        callbacks.upperBound = function(interaction, attrValue, attrName){
            
            interaction.attr('upperBound', parseFloat(attrValue));
            
            var lowerBound = interaction.attr('lowerBound');
            var upperBound = interaction.attr('upperBound');
            var sliderLength = upperBound-lowerBound; //the lenght of the slider
            var step = interaction.attr('step');
            var reverse = !!interaction.attr('reverse');
            
            //if max is smaller than min then change lowerBound to be like upperBound
            if(upperBound<lowerBound){
                callbacks.lowerBound(interaction, upperBound);
                _widget.$form.find('input[name="lowerBound"]').val(upperBound);
            }
            
            //check if the lendth of the slide is smaller than the step
            if(sliderLength<step && sliderLength>=0){
                callbacks.step(interaction, sliderLength);
                _widget.$form.find('input[name="step"]').val(sliderLength);
            }
            _widget.$form.find('input[name="step"]').incrementer('options', {max: upperBound});
            _widget.$container.find((!reverse) ? '.slider-max' : '.slider-min').text(attrValue);
            _widget.$container.find('.qti-slider').noUiSlider({ range: { 'min': interaction.attr('lowerBound'), 'max': interaction.attr('upperBound') } }, true);
        };
        
        // -- orientation Callback
        callbacks.orientation = function(interaction, attrValue, attrName){
            interaction.attr('orientation', attrValue);
            
            var orientation = (interaction.attr('orientation')) ? interaction.attr('orientation') : 'horizontal';
            var reverse = interaction.attr('reverse');
            
            _widget.$container.find('.qti-slider').noUiSlider({ 'orientation': interaction.attr('orientation') }, true);
        };
        
        // -- reverse Callback
        callbacks.reverse = function(interaction, attrValue, attrName){
            
            interaction.attr('reverse', !!attrValue);
            
            var reverse = interaction.attr('reverse');
            var lowerBound = parseInt(interaction.attr('lowerBound'));
            var upperBound = parseInt(interaction.attr('upperBound'));
            var $sliderElt = _widget.$container.find('.qti-slider');
            var start = (reverse) ? upperBound : lowerBound;
            $sliderElt.noUiSlider({ start: start }, true);
            
            _widget.$container.find('span.qti-slider-cur-value').text(lowerBound);
            _widget.$container.find('.slider-min').text(!reverse ? lowerBound : upperBound);
            _widget.$container.find('.slider-max').text(!reverse ? upperBound : lowerBound);
        };
        
        // -- step Callback
        callbacks.step = function(interaction, attrValue, attrName){
            
            var lowerBound = interaction.attr('lowerBound');
            var upperBound = interaction.attr('upperBound');
            var currentStep = parseInt(attrValue);
            var sliderLength = upperBound-lowerBound; //the lenght of the slider
            if(sliderLength>=currentStep){
                interaction.attr('step', currentStep);
                _widget.$container.find('.qti-slider').noUiSlider({ 'step': interaction.attr('step') }, true);
                _widget.$form.find('input[name="step"]').incrementer('options', {max: sliderLength});
            }
        };
        
        // -- stepLabel Callback
        callbacks.stepLabel = function(interaction, attrValue, attrName){
            
            interaction.attr('stepLabel', !!attrValue);
            
            _widget.$container.find('span.slider-middle').remove();
            
            if (interaction.attr('stepLabel')) {
                var upperBound = interaction.attr('upperBound');
                var lowerBound = interaction.attr('lowerBound');
                var step = interaction.attr('step');
                var reverse = interaction.attr('reverse');
                    
                var steps = parseInt((upperBound - lowerBound) / step);
                var middleStep = parseInt(steps / 2);
                var leftOffset = (100 / steps) * middleStep;
                var middleValue = (reverse) ? upperBound - middleStep * step : lowerBound + middleStep * step;
                _widget.$container.find('.slider-min').after('<span class="slider-middle" style="left:' + leftOffset + '%">' + middleValue + '</span>');
            }
        };
        
        formElement.setChangeCallbacks($form, interaction, callbacks);
    };
    return SliderInteractionStateQuestion;
});
