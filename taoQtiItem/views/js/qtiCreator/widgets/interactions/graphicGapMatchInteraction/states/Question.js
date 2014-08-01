/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/graphicInteractionShapeEditor',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/imageSelector',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/helpers/identifier',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/graphicGapMatch',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/choices/associableHotspot',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/choices/gapImg',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/media',
    'taoQtiItem/qtiCreator/helper/dummyElement',
    'taoQtiItem/qtiCreator/editor/editor'
], function($, _, __, GraphicHelper, stateFactory, Question, shapeEditor, imageSelector, formElement, identifierHelper, formTpl, choiceFormTpl, gapImgFormTpl, mediaTlbTpl, dummyElement, editor){

    /**
     * Question State initialization: set up side bar, editors and shae factory
     */
    var initQuestionState = function initQuestionState(){

        var widget      = this.widget;
        var interaction = widget.element;
        var options     = widget.options; 
        var paper       = interaction.paper;

        if(!paper){
            return;
        }

        var $choiceForm  = widget.choiceForm;
        var $formInteractionPanel = $('#item-editor-interaction-property-bar');
        var $formChoicePanel = $('#item-editor-choice-property-bar');

        var $left, $top, $width, $height;

        //instantiate the shape editor, attach it to the widget to retrieve it during the exit phase
        widget._editor = shapeEditor(widget, {
            shapeCreated : function(shape, type){
                var newChoice = interaction.createChoice({
                    shape  : type === 'path' ? 'poly' : type,
                    coords : GraphicHelper.qtiCoords(shape) 
                });

                //link the shape to the choice
                shape.id = newChoice.serial;
            },
            shapeRemoved : function(id){
                interaction.removeChoice(id);
            },
            enterHandling : function(shape){
                enterChoiceForm(shape.id);
            },
            quitHandling : function(){
                leaveChoiceForm();
            },
            shapeChange : function(shape){
                var bbox;
                var choice = interaction.getChoice(shape.id);
                if(choice){
                    choice.attr('coords', GraphicHelper.qtiCoords(shape));
    
                    if($left && $left.length){
                        bbox = shape.getBBox();
                        $left.val(parseInt(bbox.x, 10)); 
                        $top.val(parseInt(bbox.y, 10));
                        $width.val(parseInt(bbox.width, 10));
                        $height.val(parseInt(bbox.height, 10));                         
                    }         
                }
            }
        });
    
        //and create an instance
        widget._editor.create();

        _.forEach(interaction.getGapImgs(), setUpGapImg);
        
        createGapImgPlaceholder();

        //we need to stop the question mode on resize, to keep the coordinate system coherent, 
        //even in responsive (the side bar introduce a biais)
        $(window).on('resize.changestate', function(){
            widget.changeState('sleep');
        });

        function createGapImgPlaceholder(){
            var $gapList     = $('ul.source', widget.$original);
            var $placeholder = 
                $('<li class="empty add-option">' +
                     '<div><span class="icon-add"></span></div>' +
                   '</li>') ;
            $placeholder.on('click', function(){
                var gapImg = interaction.createGapImg({});
                gapImg.object.removeAttr('type');
                setUpGapImg(gapImg);    
            }); 
            $placeholder.appendTo($gapList);
        }

        function setUpGapImg(gapImg, update){
            var $dummy;
            var $gapList        = $('ul.source', widget.$original);
            var $placeholder    = $('.empty', $gapList);
            var $gapImg         = $('[data-serial="' + gapImg.serial + '"]', $gapList);

            if(!$gapImg.length){
                $gapImg = $("<li></li>").insertBefore($placeholder);
                $gapImg.data('serial', gapImg.serial)
                       .attr('data-serial', gapImg.serial);
            }

            if(gapImg.object && gapImg.object.attributes.data){
                if(update === true){
                     
                    $gapImg.replaceWith( gapImg.render() );
                    $gapImg = $('[data-serial="' + gapImg.serial + '"]', $gapList);
                }
            } else {
                $dummy = dummyElement.get({
                            icon: 'image',
                            css: {
                                width  : 58,
                                height : 58
                            },
                            title : __('Select an image.')
                        });
                $gapImg.addClass('placeholder qti-choice qti-gapImg')
                       .empty()
                       .append($dummy);
            }

            //prevent the creator to resize them
            $gapImg.addClass('widget-box');

            //manage gap deletion
            $(mediaTlbTpl())
              .appendTo($gapImg)
              .show()
              .click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    $gapImg.remove();
                    interaction.removeGapImg(gapImg);
            });

            $gapImg.off('click').on('click', function(){
                if($gapImg.hasClass('active')){
                    $gapImg.removeClass('active');
                    leaveChoiceForm();
                } else {
                    $('.active', $gapList).removeClass('active');
                    $gapImg.addClass('active');
                    enterGapImgForm(gapImg.serial);
    
                    //gap placeholders delegate to the upload button (opens the resource mgr)
                    if($gapImg.hasClass('placeholder')){ 
                        var $upload  = $('[data-role="upload-trigger"]', $choiceForm);
                        if($upload.length){
                            $upload.trigger('click');
                        }
                    }
                }
            });
        }

        /**
         * Set up the choice form
         * @private
         * @param {String} serial - the choice serial
         */
        function enterChoiceForm(serial){
            var choice = interaction.getChoice(serial);
            var element, bbox;

            if(choice){
                
                //get shape bounding box
                element = interaction.paper.getById(serial);
                bbox = element.getBBox();

                $choiceForm.empty().html(
                    choiceFormTpl({
                        identifier  : choice.id(),
                        fixed       : choice.attr('fixed'),
                        serial      : serial,
                        matchMin    : choice.attr('matchMin'),
                        matchMax    : choice.attr('matchMax'),
                        choicesCount: _.size(interaction.getChoices()),
                        x           : parseInt(bbox.x, 10), 
                        y           : parseInt(bbox.y, 10),
                        width       : parseInt(bbox.width, 10),
                        height      : parseInt(bbox.height, 10)                         
                    })
                );

                formElement.initWidget($choiceForm);

                //init data validation and binding
                var callbacks = formElement.getMinMaxAttributeCallbacks($choiceForm, 'matchMin', 'matchMax');
                callbacks.identifier = identifierHelper.updateChoiceIdentifier;
                callbacks.fixed = formElement.getAttributeChangeCallback();

                formElement.setChangeCallbacks($choiceForm, choice, callbacks);

                $formChoicePanel.show();
                editor.openSections($formChoicePanel.children('section'));
                editor.closeSections($formInteractionPanel.children('section'));
                
                //change the nodes bound to the position fields
                $left   = $('input[name=x]', $choiceForm);
                $top    = $('input[name=y]', $choiceForm);
                $width  = $('input[name=width]', $choiceForm);
                $height = $('input[name=height]', $choiceForm);
            }
        }
        
        /**
         * Leave the choice form
         * @private
         */
        function leaveChoiceForm(){
            if($formChoicePanel.css('display') !== 'none'){
                editor.openSections($formInteractionPanel.children('section'));
                $formChoicePanel.hide();
                $choiceForm.empty();
            }
        }
        
        /**
         * Set up the gapImg form
         * @private
         * @param {String} serial - the gapImg serial
         */
        function enterGapImgForm(serial){
            
            var callbacks,
                gapImgSelectorOptions,
                gapImg = interaction.getGapImg(serial);
            
            if(gapImg){
                
                $choiceForm.empty().html(
                    gapImgFormTpl({
                        identifier      : gapImg.id(),
                        fixed           : gapImg.attr('fixed'),
                        serial          :  serial,
                        matchMin        : gapImg.attr('matchMin'),
                        matchMax        : gapImg.attr('matchMax'),
                        choicesCount    : _.size(interaction.getChoices()),
                        baseUrl         : options.baseUrl,
                        data            : gapImg.object.attr('data'),
                        width           : gapImg.object.attr('width'),
                        height          : gapImg.object.attr('height'),
                        type            : gapImg.object.attr('type')
                    })
                );
                
                gapImgSelectorOptions = _.clone(options);
                gapImgSelectorOptions.title = __('Please select the picture from the resource manager. You can add new files from your computer with the button "Add file(s)".');
                imageSelector($choiceForm, gapImgSelectorOptions);

                formElement.initWidget($choiceForm);

                //init data validation and binding
                callbacks = formElement.getMinMaxAttributeCallbacks($choiceForm, 'matchMin', 'matchMax');
                callbacks.identifier = identifierHelper.updateChoiceIdentifier;
                callbacks.fixed = formElement.getAttributeChangeCallback();
                callbacks.data = function(element, value){
                    gapImg.object.attr('data', value);
                    setUpGapImg(gapImg, true);
                };
                callbacks.width = function(element, value){
                    gapImg.object.attr('width', value);
                };
                callbacks.height = function(element, value){
                    gapImg.object.attr('height', value);
                };
                callbacks.type = function(element, value){
                    if(!value || value === ''){
                        interaction.object.removeAttr('type');
                    } else {
                        gapImg.object.attr('type', value);
                    }
                };
                formElement.setChangeCallbacks($choiceForm, gapImg, callbacks);

                $formChoicePanel.show();
                editor.openSections($formChoicePanel.children('section'));
                editor.closeSections($formInteractionPanel.children('section'));

                if(typeof window.scroll === 'function'){
                    window.scroll(0, $choiceForm.offset().top);
                }   
            }
        }
    };

    /**
     * Exit the question state, leave the room cleaned up
     */
    var exitQuestionState = function initQuestionState(){
        var widget      = this.widget;
        var interaction = widget.element;
        var paper       = interaction.paper;

        if(!paper){
            return;
        }
        
        $(window).off('resize.changestate');

        if(widget._editor){
            widget._editor.destroy();
        }

        //remove gapImg placeholder
        $('ul.source .empty', widget.$original).remove();
        
        //restore gapImg appearance
        widget.$container.find('.qti-gapImg').removeClass('active')
                         .find('.mini-tlb').remove();
    };
    
    /**
     * The question state for the graphicGapMatch interaction
     * @extends taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Question
     */
    var GraphicGapMatchInteractionStateQuestion = stateFactory.extend(Question, initQuestionState, exitQuestionState);

    /**
     * Initialize the form linked to the interaction
     */
    GraphicGapMatchInteractionStateQuestion.prototype.initForm = function(){

        var widget = this.widget;
        var options = widget.options;
        var interaction = widget.element;
        var $form = widget.$form;

        $form.html(formTpl({
            baseUrl         : options.baseUrl,
            data            : interaction.object.attr('data'),
            width           : interaction.object.attr('width'),
            height          : interaction.object.attr('height'),
            type            : interaction.object.attr('type')
        }));

        imageSelector($form, options); 

        formElement.initWidget($form);
        
        //init data change callbacks
        var callbacks  =  {};        
        callbacks.data = function(inteaction, value){
            interaction.object.attr('data', value);
            widget.rebuild({
                ready:function(widget){
                    widget.changeState('question');
                }
            });
        };
        callbacks.width = function(inteaction, value){
            interaction.object.attr('width', value);
        };
        callbacks.height = function(inteaction, value){
            interaction.object.attr('height', value);
        };
        callbacks.type = function(inteaction, value){
            if(!value || value === ''){
                interaction.object.removeAttr('type');
            } else {
                interaction.object.attr('type', value);
            }
        };
        formElement.setChangeCallbacks($form, interaction, callbacks, { validateOnInit : false });
    };

    return GraphicGapMatchInteractionStateQuestion;
});
