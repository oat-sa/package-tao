define([
    'lodash',
    'jquery',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/extendedTextInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'i18n',
    'ckeditor',
    'ckConfigurator',
    'polyfill/placeholders'
], function(_, $, tpl, Helper, __, ckEditor ,ckConfigurator){


    /**
     * Setting the pattern mask for the input, for browsers which doesn't support this feature
     * @param {jQuery} $element
     * @param {string} pattern
     * @returns {undefined}
     */
    var _setPattern = function($element, pattern){
        var patt = new RegExp('^'+pattern+'$');
        
        //test when some data is entering in the input field
        //@todo plug the validator + tooltip
        $element.on('keyup', function(){
            $element.removeClass('field-error');
            if(!patt.test($element.val())){
                $element.addClass('field-error');
            }
        });
    };
    
    /**
     * Whether or not multiple strings are expected from the candidate to
     * compose a valid response.
     * 
     * @param {object} interaction
     * @returns {boolean}
     */
    var _isMultiple = function(interaction) {
        var attributes = interaction.getAttributes();
        var response = interaction.getResponseDeclaration();
        return !!(attributes.maxStrings && (response.attr('cardinality') === 'multiple' || response.attr('cardinality') === 'ordered'));
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10296
     * 
     * @param {object} interaction
     */
    var render = function(interaction){
        var attributes = interaction.getAttributes();
        var $container = interaction.getContainer();
        var response = interaction.getResponseDeclaration();
        var multiple = _isMultiple(interaction);
        
        var $el, expectedLength, expectedLines, placeholderType;
 
        // ckEditor config event.
        ckEditor.on('instanceCreated', function(event) {
            var editor = event.editor,
                toolbarType = 'block';

            editor.on('configLoaded', function(e) {
                editor.config = ckConfigurator.getConfig(editor, toolbarType, ckeOptions);
            });
            
            editor.on('change', function(e) {
                Helper.triggerResponseChangeEvent(interaction, {});
            });
        });
        
        // if the input is textarea
        if (!multiple) {
            $el = $container.find('textarea');
            var ckeOptions = {
               extraPlugins: 'confighelper',
               resize_enabled: true
            };
            
            //setting the placeholder for the textarea
            if (attributes.placeholderText) {
                $el.attr('placeholder', attributes.placeholderText);            
            }

            // Enable ckeditor only if text format is 'xhtml'.
            if (_getFormat(interaction) == 'xhtml') {
                //replace the textarea with ckEditor
                ckEditor.replace(interaction.attr('identifier'), ckeOptions);
                $container.find('#text-container').addClass('solid');
            }
            else {
                $el.bind('keyup change', function(e) {
                    Helper.triggerResponseChangeEvent(interaction, {});
                })
            }
        } 
        else {
            $el = $container.find('input');
            
            //setting the checking for minimum number of answers
            if (attributes.minStrings) {
                
                //get the number of filled inputs
                var _getNumStrings = function($element) {
                    
                    var num = 0;
                    
                    $element.each(function() {
                        if ($(this).val() !== '') {
                            num++;
                        }
                    });
                    
                    return num;
                };
                
                var minStrings = parseInt(attributes.minStrings);

                if (minStrings > 0) {
                    
                    $el.on('blur', function() {
                        setTimeout(function() {
                            //checking if the user was clicked outside of the input fields
                            if (!$el.is(':focus') && _getNumStrings($el) < minStrings) {
                                Helper.appendNotification(interaction, __('The minimum number of answers is ') + ' : ' + minStrings, 'warning');
                            }
                        }, 100);
                    });
                }
            }
            
            //set the fields width
            if (attributes.expectedLength) {
                expectedLength = parseInt(attributes.expectedLength, 10);

                if (expectedLength > 0) {
                    $el.each(function() {
                        $(this).css('width', expectedLength + 'em');
                    });
                }
            }
            
            //set the fileds pattern mask
            if (attributes.patternMask) {
                $el.each(function() {
                    _setPattern($(this), attributes.patternMask);
                });
            }
            
            //set the fileds placeholder
            if (attributes.placeholderText) {
                /**
                 * The type of the fileds placeholder:
                 * multiple - set placeholder for each field
                 * first - set placeholder only for first field
                 * none - dont set placeholder
                 */
                placeholderType = 'first';
                
                if (placeholderType === 'multiple') {
                    $el.each(function() {
                        $(this).attr('placeholder', attributes.placeholderText);
                    });
                }
                else if (placeholderType === 'first') {
                    $el.first().attr('placeholder', attributes.placeholderText);
                }
            }
        }
    };
    
    var resetResponse = function(interaction) {
        interaction.getContainer().find('input, textarea').val('');
    };

    /**
     * Set the response to the rendered interaction.
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10296
     * 
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response) {

        var _setMultipleVal = function(identifier, value) {
            interaction.getContainer().find('#'+identifier).val(value);
        };
        
        var _setVal = function(value) {
            interaction.getContainer().find('textarea').val(value);
        };

        var baseType = interaction.getResponseDeclaration().attr('baseType');
        
        if (response.base && response.base[baseType] !== undefined) {
            _setVal(response.base[baseType]);
        }
        else if (response.list && response.list[baseType]) {
            
            for (var i in response.list[baseType]) {
                var identifier = (response.list.identifier === undefined) ? '' : response.list.identifier[i];
                _setMultipleVal(identifier + '_' + i, response.list[baseType][i]);
            }
            
        } 
        else {
            throw new Error('wrong response format in argument.');
        }
    };

    /**
     * Return the response of the rendered interaction
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10296
     * 
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction) {
        
        var $container = interaction.getContainer();
        var attributes = interaction.getAttributes();
        var responseDeclaration = interaction.getResponseDeclaration();
        var baseType = responseDeclaration.attr('baseType');
        var numericBase = attributes.base || 10;
        var multiple = !!(attributes.maxStrings && (responseDeclaration.attr('cardinality') === 'multiple' || responseDeclaration.attr('cardinality') === 'ordered'));
        var ret = multiple ? {list:{}} : {base:{}};
    
        if (multiple) {
            
            var values = [];
            
            $container.find('input').each(function(i) {
                
                $el = $(this);
                
                if (attributes.placeholderText && $el.val() === attributes.placeholderText) {
                    values[i] = '';
                }
                else {
                    if (baseType === 'integer') {
                        values[i] = parseInt($el.val(), numericBase);
                        values[i] = isNaN(values[i]) ? '' : values[i];
                    }
                    else if(baseType === 'float') {
                        values[i] = parseFloat($el.val());
                        values[i] = isNaN(values[i]) ? '' : values[i];
                    }
                    else if(baseType === 'string') {
                        values[i] = $el.val();
                    }
                }
            });

            ret.list[baseType] = values;
        } 
        else {
            
            var value = '';
            
            if (attributes.placeholderText && _getTextareaValue(interaction) === attributes.placeholderText) {
                value = '';
            }
            else {
                
                if (baseType === 'integer') {
                    value = parseInt(_getTextareaValue(interaction), numericBase);
                }
                else if (baseType === 'float') {
                    value = parseFloat(_getTextareaValue(interaction));
                }
                else if (baseType === 'string') {
                    value = _getTextareaValue(interaction);
                }
            }

            ret.base[baseType] = isNaN(value) && typeof value === 'number' ? '' : value;
        }
        
        return ret;
    };
    
    var _getTextareaValue = function(interaction) {
        if (_getFormat(interaction) === 'xhtml') {
            return _ckEditorData(interaction);
        }
        else {
            return Helper.getContainer(interaction).find('textarea').val();
        }
    }
    
    var _ckEditorData = function(interaction) {
        return ckEditor.instances[interaction.attr('identifier')].getData();
    };
    
    var _getFormat = function(interaction) {
        var format = interaction.attr('format');
        
        switch (format) {
            case 'plain':
            case 'xhtml':
            case 'preFormatted':
                return format;
            break;
            
            default:
                return 'plain';
        }
    };
    
    var updateFormat = function(interaction, from) {
        var $container = Helper.getContainer(interaction);
        
        if (interaction.attr('format') == 'xhtml') {
            ckEditor.replace(interaction.attr('identifier'));
        }
        else {
            // preFormatted or plain
            if (from == 'xhtml') {
                ckEditor.instances[interaction.attr('identifier')].destroy();
            }
        }
    };
    
    var enable = function(interaction) {
        var $container = Helper.getContainer(interaction);
        $container.find('input, textarea').removeAttr('disabled');
        
        if (interaction.attr('format') == 'xhtml') {
            ckEditor.instances[interaction.attr('identifier')].destroy();
            ckEditor.replace(interaction.attr('identifier'));
        }
    };
    
    var disable = function(interaction) {
        var $container = Helper.getContainer(interaction);
        $container.find('input, textarea').attr('disabled', 'disabled');
        
        if (interaction.attr('format') == 'xhtml') {
            ckEditor.instances[interaction.attr('identifier')].destroy();
            ckEditor.replace(interaction.attr('identifier'));
        }
    };
    
    var clearText = function(interaction) {
        setText(interaction, '');
    };
    
    var setText = function(interaction, text) {
        var $container = Helper.getContainer(interaction);
        
        if (interaction.attr('format') == 'xhtml') {
            ckEditor.instances[interaction.attr('identifier')].setData(text);
        }
        else {
            $container.find('textarea').val(text);
        }
    }

    return {
        qtiClass : 'extendedTextInteraction',
        template : tpl,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        updateFormat : updateFormat,
        enable : enable,
        disable : disable,
        clearText : clearText,
        setText : setText
    };
});
