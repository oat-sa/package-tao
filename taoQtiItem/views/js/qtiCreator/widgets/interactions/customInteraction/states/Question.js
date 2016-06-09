define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Question',
    'jquery',
    'lib/farbtastic/farbtastic'
], function(stateFactory, Question, $){

    var CustomInteractionStateQuestion = stateFactory.extend(Question);

    CustomInteractionStateQuestion.prototype.initColorPickers = function(){

        var $colorTriggers = this.widget.$form.find('.color-trigger:not([data-color-picker=initialized])');
        
        $colorTriggers.each(function(){

            var $colorTrigger = $(this),
                $input = $colorTrigger.siblings('input'),
                color = $input.val();

            //set color recorded in the hidden input to the color trigger
            $colorTrigger.css('background-color', color);
        });

        $colorTriggers.on('click.color-picker', function(){

            var $colorTrigger = $(this),
                $context = $colorTrigger.closest('.item-editor-color-picker'),
                $container = $context.find('.color-picker-container').show(),
                $colorPicker = $container.find('.color-picker'),
                $colorPickerInput = $container.find('.color-picker-input'),
                $input = $colorTrigger.siblings('input[type="hidden"]'),
                color = $input.val();

            // Init the color picker
            $colorPicker.farbtastic('.color-picker-input', $context);

            // Set the color to the currently set on the form init
            $colorPickerInput.val(color).trigger('keyup');

            // Populate the input with the color on quitting the modal
            $container.find('.closer').off('click').on('click', function(){
                $container.hide();
                $colorPicker.off('.farbtastic');
            });
            
            //listen to color change
            $colorPicker.off('.farbtastic').on('colorchange.farbtastic', function(e, color){
                $colorTrigger.css('background-color', color);
                $input.val(color).trigger('change');
            });
            
        });
        
        $colorTriggers.attr('data-color-picker', 'initialized');
    };

    CustomInteractionStateQuestion.prototype.destroyColorPickers = function(){
        
        var $colorTriggers = this.widget.$form.find('.color-trigger');
        $colorTriggers.off('.color-picker');
        $colorTriggers.removeAttr('data-color-picker');
    };

    return CustomInteractionStateQuestion;
});