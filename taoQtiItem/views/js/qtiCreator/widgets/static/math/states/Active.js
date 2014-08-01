define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'taoQtiItem/qtiCreator/editor/MathEditor',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/math',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline',
    'lodash',
    'i18n',
    'mathJax'
], function(stateFactory, Active, MathEditor, formTpl, formElement, inlineHelper, _, __, mathJax){

    var _throttle = 300;

    var MathActive = stateFactory.extend(Active, function(){

        this.initForm();

    }, function(){

        this.widget.$form.empty();
    });

    MathActive.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            math = _widget.element,
            mathML = math.mathML || '',
            tex = math.getAnnotation('latex') || '',
            display = math.attr('display') || 'inline',
            editMode = 'latex';

        if(!tex.trim() && mathML.trim()){
            editMode = 'mathml';
        }

        $form.html(formTpl({
            mathjax : !!mathJax,
            editMode : editMode,
            latex : tex,
            mathml : mathML
        }));

        if(mathJax){

            //init select boxes
            $form.find('select[name=display]').val(display);
            $form.find('select[name=editMode]').val(editMode);

            $form.children('.panel[data-role="' + editMode + '"]').show();
            _toggleMode($form, editMode);

            //... init standard ui widget
            formElement.initWidget($form);

            this.initFormChangeListener();

        }

    };

    MathActive.prototype.initFormChangeListener = function(){

        var _widget = this.widget,
            $container = _widget.$container,
            $form = _widget.$form,
            math = _widget.element,
            mathML = math.mathML,
            tex = math.getAnnotation('latex'),
            display = math.attr('display') || 'inline',
            $fields = {
            mathml : $form.find('textarea[name=mathml]'),
            latex : $form.find('input[name=latex]')
        },
        $triggers = $form.find('.math-editor-trigger'),
            $popup = $form.find('#math-editor-container'),
            $largeFields = {
            mathml : $popup.find('textarea[data-target="mathml"]'),
            latex : $popup.find('input[data-target="latex"]')
        },
        $title = $popup.find('h3'),
            $closer = $popup.find('.closer'),
            $dragHandle = $popup.find('.dragger'),
            $modeSelector = $form.find('select[name=editMode]');

        // display popup
        $triggers.on('click', function(e){
            e.preventDefault();
            $popup.hide();

            var target = this.hash.substring(1),
                $partner = $fields[target],
                $largeField = $largeFields[target];

            // set title
            $title.text($partner.parent().find('label').text());

            // copy value
            $largeField.val($partner.val());

            $partner.prop('disabled', true);
            $modeSelector.prop('disabled', true);
            $popup.data('target', target);
            $popup.removeClass('mathml latex').addClass(target);

            $popup.removeAttr('style');

            $popup.show();

            $triggers.hide();
        });

        // permanently copy to original field
        _($largeFields).forEach(function($largeField, target){
            $largeField.on('keyup', function(){
                $fields[target].val($largeFields[target].val());
                $fields[target].trigger('keyup');
            });
            $largeField.attr('placeholder', $fields[target].attr('placeholder'));
        });

        // hide popup
        $closer.on('click', function(){
            var target = $popup.data('target');
            $fields[target].val($largeFields[$popup.data('target')].val());
            $fields[target].prop('disabled', false);
            $modeSelector.prop('disabled', false);
            $popup.hide();
            $triggers.show();
        });

        // drag popup
        $popup.draggable({
            handle : $dragHandle
        });


        var mathEditor = new MathEditor({
            tex : tex,
            mathML : mathML,
            display : display,
            buffer : $form.find('.math-buffer'),
            target : _widget.$original
        });

        //init data change callbacks
        formElement.setChangeCallbacks($form, math, {
            display : function(m, value){
                if(value === 'block'){
                    m.attr('display', 'block');
                }else{
                    m.removeAttr('display');
                }
                _widget.rebuild({
                    ready : function(widget){
                        widget.changeState('active');
                    }
                });
            },
            editMode : function(m, value){

                _toggleMode($form, value);
            },
            latex : _.throttle(function(m, value){

                mathEditor.setTex(value).renderFromTex(function(){

                    //saveTex
                    m.setAnnotation('latex', value);

                    //update mathML
                    $fields.mathml.html(mathEditor.mathML);
                    m.setMathML(mathEditor.mathML);

                    inlineHelper.togglePlaceholder(_widget);

                    $container.change();
                });

            }, _throttle),
            mathml : _.throttle(function(m, value){

                mathEditor.setMathML(value).renderFromMathML(function(){

                    //save mathML
                    m.setMathML(value);

                    //clear tex:
                    $fields.latex.val('');
                    m.removeAnnotation('latex');

                    inlineHelper.togglePlaceholder(_widget);

                    $container.change();
                });

            }, _throttle)
        });
    };

    var _toggleMode = function($form, mode){

        var $panels = {
            mathml : $form.children('.panel[data-role="mathml"]'),
            latex : $form.children('.panel[data-role="latex"]')
        },
        $fields = {
            mathml : $form.find('textarea[name=mathml]'),
            latex : $form.find('input[name=latex]')
        },
        $editMode = $form.find('select[name=editMode]');

        //toggle form visibility
        $panels.mathml.hide();
        $panels.latex.hide();
        if(mode === 'latex'){
            $panels.latex.show();
        }else if(mode === 'mathml'){
            $panels.mathml.show();
            if($fields.latex.val()){
                //show a warning here, stating that the content in LaTeX will be removed
                if(!$fields.mathml.hasClass('tooltipstered')){
                    _createWarningTooltip($fields.mathml);
                }
                $fields.mathml.tooltipster('show');
                $editMode.off('change.editMode').one('change.editMode', function(){
                    $fields.mathml.tooltipster('hide');
                });
            }
        }
    };


    var _createWarningTooltip = function($mathField){

        var $content = $('<span>')
            .html(__('Currently conversion from MathML to LaTeX is not available. Editing MathML here will have the LaTex code discarded.'));

        $mathField.tooltipster({
            theme : 'tao-warning-tooltip',
            content : $content,
            delay : 200,
            trigger : 'custom'
        });

        $mathField.on('focus.mathwarning', function(){
            $mathField.tooltipster('hide');
        });

        setTimeout(function(){
            $mathField.tooltipster('hide');
        }, 3000);
    };

    return MathActive;
});