function MathEditor(htmlEditor, mathSerial){

    this.htmlEditor = htmlEditor;
    this.mathSerial = mathSerial;
    this.$form = null;
    this.mathML = '';
    this.tex = '';
    this.display = '';

    var mathEditor = this;

    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/editMath",
        dataType : 'json',
        data : {
            type : mathEditor.htmlEditor.type,
            serial : mathEditor.htmlEditor.serial,
            objectSerial : mathEditor.mathSerial
        },
        success : function(data){

            $('<div id="editObjectForm" title="' + data.title + '">' + data.html + '</div>').dialog({
                modal : true,
                width : 640,
                height : 580,
                buttons : [
                    {
                        text : __('Save'),
                        click : function(){
                            mathEditor.save(mathEditor.mathML, mathEditor.tex, mathEditor.display);
                        }
                    },
                    {
                        text : __('Save & Close'),
                        click : function(){
                            var $modal = $(this);
                            mathEditor.save(mathEditor.mathML, mathEditor.tex, mathEditor.display, function(){
                                $modal.dialog('close');
                            });
                        }
                    },
                    {
                        text : __('Close'),
                        click : function(){
                            $(this).dialog('close');
                        }
                    }
                ],
                close : function(){
                    //delete all element, math jax fails on new instance otherwise
                    $(this).empty();
                },
                open : function(){

                    //reference form element
                    mathEditor.$form = $(this);
                    mathEditor.$form.on('submit', function(){
                        return false;
                    });

                    //reference form elts:
                    var $eltMathML = mathEditor.$form.find('textarea#mathML');
                    var $eltTex = mathEditor.$form.find('input#tex');
                    var $eltAuthoring = mathEditor.$form.find('input:radio[name=authoring]');

                    //reference preview boxes:
                    var $texPreviewBox = $("#texReviewBox");
                    var $mathPreviewBox = $("#mathReviewBox");

                    //programatically add the tips:
                    $eltMathML.parent('div').prepend('<p class="qti-form-tip">' + data.tips.math + '</p>');
                    $eltTex.parent('div').prepend('<p class="qti-form-tip">' + data.tips.tex + '</p>');

                    mathEditor.$form.find('input:radio[name=display]').on('change', function(){
                        mathEditor.display = mathEditor.$form.find('input:radio[name=display]:checked').val();
                    });

                    if(typeof(MathJax) === 'undefined'){
                        mathEditor.$form.find('#formObject_errors').show().find('a').click(function(e){
                            e.preventDefault();
                            window.open($(this).attr('href'), 'Enable Math in QTI Items');
                        });
                    }

                    var switchToMathML = function(){
                        $eltTex.parent('div').hide();
                        $texPreviewBox.hide();
                        $eltMathML.val(mathEditor.mathML).parent('div').show();
                        mathEditor.previewMathML(mathEditor.mathML, $mathPreviewBox, true);
                        $mathPreviewBox.show();
                    };

                    var swithToTex = function(){
                        $eltMathML.parent('div').hide();
                        $mathPreviewBox.hide();
                        mathEditor.previewTex($eltTex.val(), $texPreviewBox);
                        $eltTex.parent('div').show();
                        $texPreviewBox.show();
                    };

                    $eltAuthoring.on('change', function(){
                        if($eltTex.val() || $eltMathML.val()){
                            var $dialog = $('#dialog-confirm');
                            if(mathEditor.$form.find('input:radio[name=authoring]:checked').val() === 'math'){
                                $dialog.find('p#dialog-confirm-message').html(data.tips.mathWarning);
                                $dialog.dialog({
                                    title : data.tips.mathSwitch,
                                    resizable : false,
                                    height : 280,
                                    width : 430,
                                    modal : true,
                                    buttons : {
                                        'Keep Editing LaTeX' : function(){
                                            mathEditor.$form.find('input:radio[value=tex]').prop('checked', true);
                                            $(this).dialog('close');
                                        },
                                        'Edit MathML' : function(){
                                            switchToMathML();
                                            mathEditor.$form.find('input:radio[value=math]').prop('checked', true);
                                            $(this).dialog('close');
                                        }
                                    }
                                });
                            }else{
                                $dialog.find('p#dialog-confirm-message').html(data.tips.texWarning);
                                $dialog.dialog({
                                    title : data.tips.texSwitch,
                                    resizable : false,
                                    height : 280,
                                    width : 430,
                                    modal : true,
                                    buttons : {
                                        'Keep Editing MathML' : function(){
                                            mathEditor.$form.find('input:radio[value=math]').prop('checked', true);
                                            $(this).dialog('close');
                                        },
                                        'Edit LaTeX' : function(){
                                            swithToTex();
                                            mathEditor.$form.find('input:radio[value=tex]').prop('checked', true);
                                            $(this).dialog('close');
                                        }
                                    }
                                });
                            }
                            $dialog.siblings('div.ui-dialog-titlebar').find('a.ui-dialog-titlebar-close').hide();
                        }else{
                            if(mathEditor.$form.find('input:radio[name=authoring]:checked').val() === 'math'){
                                switchToMathML();
                            }else{
                                swithToTex();
                            }
                        }
                    });

                    if(mathEditor.$form.find('input:radio[name=authoring]:checked').val() === 'math'){
                        switchToMathML();
                    }else{
                        swithToTex();
                    }

                    //init mathJax
                    mathEditor.texJax = null;
                    $eltTex.on('keyup', function(){
                        mathEditor.previewTex($(this).val(), $texPreviewBox);
                    });

                    $eltMathML.on('keyup', function(){
                        $eltTex.val('');//empty LaTeX box
                        mathEditor.setTex('');
                        mathEditor.previewMathML($(this).val(), $mathPreviewBox);
                    });

                    //init values
                    mathEditor.mathML = data.math.mathML || '';
                    $eltMathML.val(data.math.mathML);
                    mathEditor.tex = data.math.tex || $eltTex.val();
                    mathEditor.display = data.math.display || mathEditor.$form.find('input:radio[name=display]:checked').val();
                    if(mathEditor.mathML){
                        mathEditor.previewMathML(mathEditor.mathML, $mathPreviewBox, true);
                    }
                    if(mathEditor.tex){
                        mathEditor.previewTex(mathEditor.tex, $texPreviewBox);
                    }
                }
            });

        }
    });

}
MathEditor.prototype.previewMathML = function(strMath, $mathPreviewBox, force){

    if(typeof(MathJax) !== 'undefined'){
        if(this.running){
            return;
        }

        //strip and wrap math tags to compare and clean it
        strMath = MathEditor.stripMathTags(strMath);
        if(!force && strMath === this.mathML){
            return;
        }

        strMath = MathEditor.wrapMathTags(strMath);
        var $buffer = $('#mathReviewBuffer');
        $buffer.html(strMath);

        var mathEditor = this;
        MathJax.Hub.Queue(
            ["Typeset", MathJax.Hub, $buffer[0]],
            function(){
                mathEditor.running = false;
                $mathPreviewBox.html($buffer.html());
                mathEditor.mathML = MathEditor.stripMathTags(strMath);
            }
        );
    }

}

MathEditor.prototype.previewTex = function(strTeX, $texPreviewBox){

    if(typeof(MathJax) !== 'undefined'){
        var mathEditor = this;
        var jaxQueue = MathJax.Hub.queue;

        if(!mathEditor.texJax){
            //programmatically typeset the mathOutput element and fetch the first one
            jaxQueue.Push(
                ["Typeset", MathJax.Hub, $texPreviewBox[0]],
                function(){
                    mathEditor.texJax = MathJax.Hub.getAllJax("mathOutput")[0];
                    $texPreviewBox.css('visibility', 'hidden');
                }
            );
        }

        //render preview:
        jaxQueue.Push(
            function(){
                $texPreviewBox.css('visibility', 'hidden');
            },
            ["Text", mathEditor.texJax, "\\displaystyle{" + strTeX + "}"],
            function(){
                mathEditor.setTex(strTeX);
                mathEditor.currentTexToMathML(function(mathML){
                    mathEditor.setMathML(MathEditor.stripMathTags(mathML));
                });
            },
            function(){
                $texPreviewBox.css('visibility', 'visible');
            }
        );
    }

}

MathEditor.stripMathTags = function(mathMLstr){
    mathMLstr = mathMLstr.replace(/<(\/)?math[^>]*>/g, '');
    mathMLstr = mathMLstr.replace(/^\s*[\r\n]/gm, '');//remove first blank line
    mathMLstr = mathMLstr.replace(/\s*[\r\n]$/gm, '');//last blank line
    return mathMLstr;
}

MathEditor.wrapMathTags = function(mathMLstr){
    if(!mathMLstr.match(/<math[^>]*>/)){
//        var display = (this.display === 'block') ? ' display="block"' : '';
        mathMLstr = '<math display="block">' + mathMLstr;//always show preview in block mode
    }
    if(!mathMLstr.match(/<\/math[^>]*>/)){
        mathMLstr += '</math>';
    }
    return mathMLstr;
}

MathEditor.prototype.setMathML = function(mathMLstr){
    this.mathML = mathMLstr;
}

MathEditor.prototype.setTex = function(texStr){
    this.tex = texStr;
}

MathEditor.prototype.currentTexToMathML = function(callback){
    var mathEditor = this;
    var mathML = '';
    try{
        mathML = mathEditor.texJax.root.toMathML('');
    }catch(err){
        if(!err.restart){
            throw err;
        }
        return MathJax.Callback.After(function(){
            mathEditor.currentTexToMathML(callback);
        }, err.restart);
    }
    MathJax.Callback(callback)(mathML);
}

MathEditor.prototype.save = function(mathML, tex, display, callback){
    var mathEditor = this;
    qtiEdit.ajaxRequest({
        type : "POST",
        url : root_url + "taoQTI/QtiAuthoring/saveMath",
        dataType : 'json',
        data : {
            type : this.htmlEditor.type,
            serial : this.htmlEditor.serial,
            objectSerial : this.mathSerial,
            mathML : mathML,
            tex : tex,
            display : display
        },
        success : function(data){
            if(data.saved){
                mathEditor.htmlEditor.rebuildQtiElementPlaceholders({
                    'between' : function(content){
                        var regex = new RegExp('{{qtiMath:(block|inline):' + mathEditor.mathSerial + '}}', 'img');
                        return content.replace(regex, '{{qtiMath:' + (data.display === 'block' ? 'block' : 'inline') + ':' + mathEditor.mathSerial + '}}');
                    }
                });
                if(typeof(callback) === 'function'){
                    callback();
                }
            }
        }
    });
};