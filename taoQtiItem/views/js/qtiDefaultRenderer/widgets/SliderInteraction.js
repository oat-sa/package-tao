define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget', 'jqueryui', 'css!tao/../css/custom-theme/jquery-ui-1.8.22.custom'], function(Widget){

    var SliderInteraction = Widget.extend({
        render : function(){

            var ctx = this;

            //add the containers
            $('#' + ctx.id).append("<input type='hidden' id='" + ctx.id + "_qti_slider_value' />")
                .append("<div class='qti_slider'></div>")
                .append("<div class='qti_slider_label'></div>");
            //the input that always holds the slider value
            ctx.$sliderVal = $('#' + ctx.id + "_qti_slider_value");

            var containerWidth = parseInt($('#' + ctx.id).width());

            //get the options
            var min = parseInt(ctx.options['lowerBound']);
            var max = parseInt(ctx.options['upperBound']);

            var step = 1;//default value as per QTI standard
            if(ctx.options.step != undefined || ctx.options.step !== null){
                step = parseInt(ctx.options.step) ? parseInt(ctx.options.step) : 0.001;//if step is null, should simulate pseudo continuous step
            }
            var stepLabel = (ctx.options['stepLabel'] === true || ctx.options['stepLabel'] === 'true');
            stepLabel = false;
            var reverse = (ctx.options['reverse'] === true || ctx.options['reverse'] === 'true');
            var orientation = 'horizontal';
            if($.inArray(ctx.options['orientation'], ['horizontal', 'vertical']) > -1){
                orientation = ctx.options['orientation'];
            }

            //calculate and adapt the slider size
            var sliderSize = ((max - min) / step) * 20;

            if(orientation === 'horizontal'){
                if(sliderSize > containerWidth){
                    sliderSize = containerWidth - 20;
                }
                $('#' + ctx.id).addClass('qti_slider_horizontal');
                $('#' + ctx.id + ' .qti_slider').width(sliderSize + 'px');
                $('#' + ctx.id + ' .qti_slider_label').width((sliderSize + 40) + 'px');
            }else{
                var maxHeight = 300;
                if(sliderSize > maxHeight){
                    sliderSize = maxHeight;
                }
                $('#' + ctx.id).addClass('qti_slider_vertical');
                $('#' + ctx.id + ' .qti_slider').height(sliderSize + 'px');
                $('#' + ctx.id + ' .qti_slider_label').height((sliderSize + 20) + 'px');
            }

            //mark the bounds
            if(!stepLabel){
                var displayMin = min;
                var displayMax = max;
                if(reverse){
                    displayMin = max;
                    displayMax = min;
                }
                $('#' + ctx.id + ' .qti_slider_label')
                    .append("<span class='slider_min'>" + displayMin + "</span>")
                    .append("<span class='slider_cur highlight'>" + displayMin + "</span>")
                    .append("<span class='slider_max'>" + displayMax + "</span>");

            }else{
                //add a label to the steps, 
                //if there not the place we calculate the better interval
                $('#' + ctx.id).addClass('qti_slider_step');

                var stepSpacing = 20;
                var displayRatio = 1;
                if((max * 20) > sliderSize){
                    do{
                        stepSpacing = (sliderSize / (max / displayRatio));
                        displayRatio++;
                    }while(stepSpacing < 25);
                    displayRatio--;
                }
                var i = 0;
                var stepWidth = (step < 1) ? 1 : step;
                for(i = min; i <= max; i += (stepWidth * displayRatio)){
                    var displayIndex = i;
                    if(reverse){
                        displayIndex = max + min - i;
                    }
                    var $iStep = $("<span class=\"_" + displayIndex + "\">" + displayIndex + "</span>");
                    if(orientation === 'horizontal'){
                        $iStep.css({left : ((i / displayRatio) * stepSpacing) + 5 + 'px'});
                    }
                    else{
                        $iStep.css({top : ((i / displayRatio) * stepSpacing) + 5 + 'px'});
                    }
                    $('#' + ctx.id + ' .qti_slider_label').append($iStep);
                }

                //always add the last step
                i -= (step * displayRatio);
                if(i != max){
                    if(i < max){
                        $('#' + ctx.id + ' .qti_slider_label span:last').remove();
                    }
                    var displayMax = max;
                    if(reverse){
                        displayMax = min;
                    }
                    var $iStep = $("<span>" + displayMax + "</span>");
                    if(orientation === 'horizontal'){
                        $iStep.css({right : '0px'});
                    }
                    else{
                        $iStep.css({bottom : '0px'});
                    }
                    $('#' + ctx.id + ' .qti_slider_label').append($iStep);
                }
            }

            //set the start value
            var val = min;
            if((reverse && orientation === 'horizontal') || (!reverse && orientation === 'vertical')){
                val = max;
            }

            //create the slider
            ctx.slider = $('#' + ctx.id + ' .qti_slider').slider({
                value : ((reverse && orientation === 'horizontal') || (!reverse && orientation === 'vertical')) ? (max + min) - val : val,
                min : min,
                max : max,
                step : step,
                orientation : orientation,
                animate : 'fast',
                slide : function(event, ui){
                    var val = ui.value;
                    if((reverse && orientation === 'horizontal') || (!reverse && orientation === 'vertical')){
                        val = (max + min) - ui.value;
                    }
                    val = Math.round(val * 1000) / 1000;
                    ctx.$sliderVal.val(val);
                    ctx.highlight(val);
                }
            });
            ctx.$sliderVal.val(val);
            ctx.highlight(val);
        },
        setResponse : function(values){
            this.setValue(values);
        },
        getResponse : function(){
            return this.resultCollector.slider();
        },
        setValue : function(value){
            value = value + 0;//converting to numeric value
            this.$sliderVal.val(value);
            this.slider.slider('value', value);
            this.highlight(value);
        },
        highlight : function(value){
            if(!$('#' + this.id).hasClass('qti_slider_step')){
                $('#' + this.id + ' ' + '.slider_cur').text(value);
            }else{
                $('#' + this.id + ' .qti_slider_label span').removeClass('highlight');
                $('#' + this.id + ' .qti_slider_label span._' + value).addClass('highlight');
            }
        }
    });

    return SliderInteraction;
});
