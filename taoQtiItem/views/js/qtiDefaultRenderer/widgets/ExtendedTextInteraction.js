define(['taoQtiItem/qtiDefaultRenderer/widgets/StringInteraction'], function(StringInteraction){

    var ExtendedTextInteraction = StringInteraction.extend({
        init : function(interaction){
            this._super(interaction);
            this.options.multiple = false;
            var tagname = $('#' + this.id).prop('tagName').toLowerCase();
            if(tagname === 'div'){//usual case: one textarea vs special case, multiple text inputs
                this.options.multiple = true;
            }

        },
        render : function(){

            if(!this.options.multiple){
                //usual case: one textarea 
                //
                //adapt the field length
                if(this.options['expectedLength'] || this.options['expectedLines']){
                    var baseWidth = parseInt($('#' + this.id).css('width')) | 640;
                    var baseHeight = parseInt($('#' + this.id).css('height')) | 100;
                    if(this.options['expectedLength']){
                        var expectedLength = parseInt(this.options['expectedLength']);
                        if(expectedLength > 0){
                            var width = expectedLength * 10;
                            if(width > baseWidth){
                                var height = (width / baseWidth);
                                if(height > baseHeight){
                                    $('#' + this.id).css('height', height + 'em');
                                }
                            }
                        }
                    }
                    if(this.options['expectedLines']){
                        $('#' + this.id).css('height', parseInt(this.options['expectedLines']) + 'em');
                    }
                }
                this._super();
            }else{
                //adapt the fields length
                if(this.options['expectedLength']){
                    var expectedLength = parseInt(this.options['expectedLength']);
                    if(expectedLength > 0){
                        $('#' + this.id + ' :text').css('width', expectedLength + 'em');//for indication only, not constraint on answer!s
                    }
                }
                //apply the pattern to all fields
                if(this.options['patternMask']){
                    var pattern = new RegExp("/^" + this.options['patternMask'] + "$/");
                    $('#' + this.id + ' :text').change(function(){
                        $(this).removeClass('field-error');
                        if(!pattern.test($(this).val())){
                            $(this).addClass('field-error');
                        }
                    });
                }
            }
        },
        setResponse : function(values){

            if(!this.options.multiple){
                //usual case: one textarea 
                this._super(values);
            }else{
                //multiple text inputs
                if(typeof(values) === 'object'){
                    for(var i in values){
                        var value = values[i];
                        if(typeof(value) === 'string' && value != ''){
                            $('#' + this.id + " :text" + '#' + this.id + "_" + i).val(value);
                        }
                    }
                }
            }

        }
    });
    
    return ExtendedTextInteraction;
});