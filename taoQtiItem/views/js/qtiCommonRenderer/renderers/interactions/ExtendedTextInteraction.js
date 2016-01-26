/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'core/promise',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/extendedTextInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'ckeditor',
    'taoQtiItem/qtiCommonRenderer/helpers/ckConfigurator',
    'polyfill/placeholders'
], function($, _, __, Promise, tpl, containerHelper, instructionMgr, ckEditor, ckConfigurator){
    'use strict';


    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10296
     *
     * @param {Object} interaction - the extended text interaction model
     * @returns {Promise} rendering is async
     */
    var render = function render (interaction){
        return new Promise(function(resolve, reject){

            var $el, expectedLength, minStrings, expectedLines, patternMask, placeholderType, editor;
            var $container = containerHelper.get(interaction);

            var response = interaction.getResponseDeclaration();
            var multiple = _isMultiple(interaction);
            var limiter  = inputLimiter(interaction);

            var placeholderText = interaction.attr('placeholderText');

            var toolbarType = 'extendedText';
            var ckOptions = {
                'extraPlugins': 'onchange',
                'language': 'en',
                'defaultLanguage': 'en',
                'resize_enabled': true,
                'secure': location.protocol == 'https:',
                'forceCustomDomain' : true
            };

            if(!multiple){

                $el = $container.find('textarea');
                if (placeholderText) {
                    $el.attr('placeholder', placeholderText);
                }
                if (_getFormat(interaction) === "xhtml") {


                    editor = _setUpCKEditor(interaction, ckOptions);
                    if(!editor){
                        reject('Unable to instantiate ckEditor');
                    }

                    editor.on('loaded', function(){
                        //it seems there's still something done after loaded, so resolved must be defered
                        _.delay(resolve, 300);
                    });
                    if(editor.status === 'ready' || editor.status === 'loaded'){
                        _.defer(resolve);
                    }
                    editor.on('configLoaded', function(e) {
                        editor.config = ckConfigurator.getConfig(editor, toolbarType, ckOptions);

                        if(limiter.enabled){
                            limiter.listenKeyPress();
                        }
                    });
                    editor.on('change', function(e) {
                        containerHelper.triggerResponseChangeEvent(interaction, {});
                    });

                } else {

                    $el.on('keyup.commonRenderer change.commonRenderer', function(e) {
                        containerHelper.triggerResponseChangeEvent(interaction, {});
                    });

                    if(limiter.enabled){
                        limiter.listenKeyPress();
                    }

                    resolve();
                }

            //multiple inputs
            } else {

                $el            = $container.find('input');
                minStrings     = interaction.attr('minStrings');
                expectedLength = interaction.attr('expectedLength');
                patternMask    = interaction.attr('patternMask');

                //setting the checking for minimum number of answers
                if (minStrings) {

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

                    minStrings = parseInt(minStrings, 10);
                    if (minStrings > 0) {

                        $el.on('blur.commonRenderer', function() {
                            setTimeout(function() {
                                //checking if the user was clicked outside of the input fields

                                //TODO remove notifications in favor of instructions

                                if (!$el.is(':focus') && _getNumStrings($el) < minStrings) {
                                    instructionMgr.appendNotification(interaction, __('The minimum number of answers is ') + ' : ' + minStrings, 'warning');
                                }
                            }, 100);
                        });
                    }
                }

                //set the fields width
                if (expectedLength) {
                    expectedLength = parseInt(expectedLength, 10);

                    if (expectedLength > 0) {
                        $el.each(function() {
                            $(this).css('width', expectedLength + 'em');
                        });
                    }
                }

                //set the fields pattern mask
                if (patternMask) {
                    $el.each(function() {
                        _setPattern($(this), patternMask);
                    });
                }

                //set the fields placeholder
                if (placeholderText) {
                    /**
                     * The type of the fileds placeholder:
                     * multiple - set placeholder for each field
                     * first - set placeholder only for first field
                     * none - dont set placeholder
                     */
                    placeholderType = 'first';

                    if (placeholderType === 'multiple') {
                        $el.each(function() {
                            $(this).attr('placeholder', placeholderText);
                        });
                    }
                    else if (placeholderType === 'first') {
                        $el.first().attr('placeholder', placeholderText);
                    }
                }
                resolve();
            }
        });
    };

    /**
     * Reset the textarea / ckEditor
     * @param {Object} interaction - the extended text interaction model
     */
    var resetResponse = function(interaction) {
        if (_getFormat(interaction) === 'xhtml') {
            _getCKEditor(interaction).setData('');
        }else{
            containerHelper.get(interaction).find('input, textarea').val('');
        }
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
     * @param {Object} interaction - the extended text interaction model
     * @param {object} response
     */
    var setResponse = function(interaction, response) {

        var _setMultipleVal = function(identifier, value) {
            interaction.getContainer().find('#'+identifier).val(value);
        };

        var baseType = interaction.getResponseDeclaration().attr('baseType');

        if (response.base && response.base[baseType] !== undefined) {
            setText(interaction, response.base[baseType]);
        }
        else if (response.list && response.list[baseType]) {

            for (var i in response.list[baseType]) {
                var serial = (response.list.serial === undefined) ? '' : response.list.serial[i];
                _setMultipleVal(serial + '_' + i, response.list[baseType][i]);
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
     * @param {Object} interaction - the extended text interaction model
     * @returns {object}
     */
    var getResponse = function(interaction) {

        var $container = containerHelper.get(interaction);
        var attributes = interaction.getAttributes();
        var responseDeclaration = interaction.getResponseDeclaration();
        var baseType = responseDeclaration.attr('baseType');
        var numericBase = attributes.base || 10;
        var multiple = !!(attributes.maxStrings && (responseDeclaration.attr('cardinality') === 'multiple' || responseDeclaration.attr('cardinality') === 'ordered'));
        var ret = multiple ? {list:{}} : {base:{}};

        if (multiple) {

            var values = [];

            $container.find('input').each(function(i) {

                var $el = $(this);

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
                    value = _getTextareaValue(interaction, true);
                }
            }

            ret.base[baseType] = isNaN(value) && typeof value === 'number' ? '' : value;
        }

        return ret;
    };

    /**
     * Creates an input limiter object
     * @param {Object} interaction - the extended text interaction
     * @returns {Object} the limiter
     */
    var inputLimiter = function userInputLimier(interaction){

        var $container     = containerHelper.get(interaction);
        var expectedLength = interaction.attr('expectedLength');
        var expectedLines  = interaction.attr('expectedLines');
        var patternMask    = interaction.attr('patternMask');
        var $textarea,
            $charsCounter,
            $wordsCounter,
            maxWords,
            maxLength;
        var enabled = false;



        if (expectedLength || expectedLines || patternMask) {

            enabled = true;

            $textarea       = $('.text-container', $container);
            $charsCounter   = $('.count-chars',$container);
            $wordsCounter   = $('.count-words',$container);

            if (patternMask !== "") {
                maxWords = _parsePattern(patternMask, 'words');
                maxLength = _parsePattern(patternMask, 'chars');
                maxWords = (_.isNaN(maxWords)) ? undefined : maxWords;
                maxLength = (_.isNaN(maxLength) ? undefined : maxLength);
            }
        }

        /**
         * The limiter instance
         */
        var limiter = {

            /**
             * Is the limiter enabled regarding the interaction configuration
             */
            enabled : enabled,

            /**
             * Listen for a key press in the interaction and limit the input if necessary
             */
            listenKeyPress : function listenKeyPress(){
                var self = this;

                var ignoreKeyCodes = [
                    8, // backspace
                    222832, // Shift + backspace in ckEditor
                    1114120, // Ctrl + backspace in ckEditor
                    1114177, // Ctrl + a in ckEditor
                    1114202, // Ctrl + z in ckEditor
                    1114200, // Ctrl + x in ckEditor
                ];
                var triggerKeyCodes = [
                    32, // space
                    13, // enter
                    2228237, // shift + enter in ckEditor
                ];


                var limitHandler = function limitHandler(e){
                    var keyCode = e && e.data ? e.data.keyCode : e.which ;
                    if ( (!_.contains(ignoreKeyCodes, keyCode) ) &&
                         (maxWords && self.getWordsCount() >= maxWords && _.contains(triggerKeyCodes, keyCode)) ||
                         (maxLength && self.getCharsCount() >= maxLength)){

                        if (e.cancel){
                            e.cancel();
                        } else {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                        }
                        return setText(interaction, _getTextareaValue(interaction, true));
                    }
                    _.defer(function(){
                        self.updateCounter();
                    });
                };

                if (_getFormat(interaction) === "xhtml") {
                    _getCKEditor(interaction).on('key', limitHandler);
                } else {
                    $textarea.on('keydown.commonRenderer', limitHandler);
                }
            },

            /**
             * Get the number of words that are actually written in the response field
             * @return {Number} number of words
             */
            getWordsCount : function getWordsCount(){
                var value = _getTextareaValue(interaction) || '';
                if(_.isEmpty(value)){
                    return 0;
                }
                return value.replace(/\s+/gi, ' ').split(' ').length;
            },

            /**
             * Get the number of characters that are actually written in the response field
             * @return {Number} number of characters
             */
            getCharsCount : function getCharsCount(){
                var value = _getTextareaValue(interaction) || '';
                return value.length;
            },


            /**
             * Update the counter element
             */
            updateCounter : function udpateCounter(){
                $charsCounter.text(this.getCharsCount());
                $wordsCounter.text(this.getWordsCount());
            }
        };


        return limiter;
    };


    /**
     * return the value of the textarea or ckeditor data
     * @param  {Object} interaction
     * @param  {Boolean} raw Tells if the returned data does not have to be filtered (i.e. XHTML tags not removed)
     * @return {String}             the value
     */
    var _getTextareaValue = function(interaction, raw) {
        if (_getFormat(interaction) === 'xhtml') {
            return _ckEditorData(interaction, raw);
        }
        else {
            return containerHelper.get(interaction).find('textarea').val();
        }
    };


    /**
     * Setting the pattern mask for the input, for browsers which doesn't support this feature
     * @param {jQuery} $element
     * @param {string} pattern
     */
    var _setPattern = function _setPattern($element, pattern){
        var patt = new RegExp('^'+pattern+'$');

        //test when some data is entering in the input field
        //@todo plug the validator + tooltip
        $element.on('keyup.commonRenderer', function(){
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
     * @param {Object} interaction - the extended text interaction model
     * @returns {Boolean} true if a multiple
     */
    var _isMultiple = function _isMultiple(interaction) {
        var attributes = interaction.getAttributes();
        var response = interaction.getResponseDeclaration();
        return !!(attributes.maxStrings && (response.attr('cardinality') === 'multiple' || response.attr('cardinality') === 'ordered'));
    };

    /**
     * Instantiate CkEditor for the interaction
     *
     * @param {Object} interaction - the extended text interaction model
     * @param {Object} [options = {}] - the CKEditor configuration options
     * @returns {Object} the ckEditor instance (or you'll be in trouble
     */
    var _setUpCKEditor = function _setUpCKEditor(interaction, options){
        var $container = containerHelper.get(interaction);
        var editor = ckEditor.replace($container.find('.text-container')[0], options || {});
        if (editor) {
            $container.data('editor', editor.name);
            return editor;
        }
    };

    /**
     * Destroy CKEditor
     *
     * @param {Object} interaction - the extended text interaction model
     * @param {Object} [options = {}] - the CKEditor configuration options
     */
    var _destroyCkEditor = function _destroyCkEditor(interaction){
        var $container = containerHelper.get(interaction);
        var name = $container.data('editor');
        var editor;
        if(name){
            editor = ckEditor.instances[name];
        }
        if(editor){
            editor.destroy();
            $container.removeData('editor');
        }
    };

    /**
     * Gets the CKEditor instance
     * @param {Object} interaction - the extended text interaction model
     * @returns {Object}  CKEditor instance
     */
    var _getCKEditor = function _getCKEditor(interaction){
        var $container = containerHelper.get(interaction);
        var name = $container.data('editor');

        return ckEditor.instances[name];
    };

    /**
     * get the text content of the ckEditor ( not the entire html )
     * @param  {object} interaction the interaction
     * @param  {Boolean} raw Tells if the returned data does not have to be filtered (i.e. XHTML tags not removed)
     * @returns {string}             text content of the ckEditor
     */
    var _ckEditorData = function _ckEditorData(interaction, raw) {
        var editor = _getCKEditor(interaction);
        var data = editor && editor.getData() || '';

        if (!raw) {
            data = _stripTags(data);
        }

        return data;
    };

    /**
     * Remove HTML tags from a string
     * @param {String} str
     * @returns {String}
     */
    var _stripTags = function _stripTags(str) {
        var tempNode = document.createElement('div');
        tempNode.innerHTML = str;
        return tempNode.textContent;
    };

    /**
     * Get the interaction format
     * @param {Object} interaction - the extended text interaction model
     * @returns {String} format in 'plain', 'xhtml', 'preformatted'
     */
    var _getFormat = function _getFormat(interaction) {
        var format = interaction.attr('format');
        if(_.contains(['plain', 'xhtml', 'preformatted'], format)){
            return format;
        }
        return 'plain';
    };

    /**
     * parse the pattern (idealy from patternMask) and return the max words / chars from the pattern
     * @param  {String} pattern String from patternMask
     * @param  {String} type    the type of information you want : words / chars
     * @returns {Number|null}    the number extracted of the pattern, or null if not found
     */
    var _parsePattern = function _parsePattern(pattern,type){
        if (pattern === undefined){return null;}

        var regexChar = /\^\[\\s\\S\]\{\d+\,(\d+)\}\$/,
        regexWords =  /\^\(\?\:\(\?\:\[\^\\s\\:\\!\\\?\\\;\\\…\\\€\]\+\)\[\\s\\:\\!\\\?\\;\\\…\\\€\]\*\)\{\d+\,(\d+)\}\$/,
        result;

        if (type === "words") {
            result = pattern.match(regexWords);
            if (result !== null && result.length > 1) {
                return parseInt(result[1],10);
            }else{
                return null;
            }
        }else if (type === "chars"){
            result = pattern.match(regexChar);

            if (result !== null && result.length > 1) {
                return parseInt(result[1],10);
            }else{
                return null;
            }
        }else{
            return null;
        }
    };

    var enable = function(interaction) {
        var $container = containerHelper.get(interaction);
        var editor;

        $container.find('input, textarea').removeAttr('disabled');

        if (_getFormat(interaction) === 'xhtml') {
            editor = _getCKEditor(interaction);
            if (editor) {
                if (editor.status === 'ready') {
                    editor.setReadOnly(false);
                } else {
                    editor.readOnly = false;
                }
            }
        }
    };

    var disable = function(interaction) {
        var $container = containerHelper.get(interaction);
        var editor;

        $container.find('input, textarea').attr('disabled', 'disabled');

        if (_getFormat(interaction) === 'xhtml') {
            editor = _getCKEditor(interaction);
            if (editor) {
                if (editor.status === 'ready') {
                    editor.setReadOnly(true);
                } else {
                    editor.readOnly = true;
                }
            }
        }
    };

    var clearText = function(interaction) {
        setText(interaction, '');
    };

    var setText = function(interaction, text) {
        var limiter = inputLimiter(interaction);

        if ( _getFormat(interaction) === 'xhtml') {
            try{
            _getCKEditor(interaction).setData(text, function(){
                if(limiter.enabled){
                    limiter.updateCounter();
                }
            });
            } catch(e){
                console.error('setText error', e);
            }
        } else {
            containerHelper.get(interaction).find('textarea').val(text);
            if(limiter.enabled){
                limiter.updateCounter();
            }
        }
    };

     /**
     * Clean interaction destroy
     * @param {Object} interaction
     */
    var destroy = function destroy(interaction){

        var $container = containerHelper.get(interaction);
        var $el = $container.find('input, textarea');

        if(_getFormat(interaction) === 'xhtml'){
            _destroyCkEditor(interaction);
        }

        //remove event
        $el.off('.commonRenderer');
        $(document).off('.commonRenderer');

        //remove instructions
        instructionMgr.removeInstructions(interaction);

        //remove all references to a cache container
        containerHelper.reset(interaction);
    };

    /**
     * Set the interaction state. It could be done anytime with any state.
     *
     * @param {Object} interaction - the interaction instance
     * @param {Object} state - the interaction state
     */
    var setState  = function setState(interaction, state){
        if(_.isObject(state)){
            if(state.response){
                try {
                    interaction.setResponse(state.response);
                } catch(e) {
                    interaction.resetResponse();
                    throw e;
                }
            }
        }
    };

    /**
     * Get the interaction state.
     *
     * @param {Object} interaction - the interaction instance
     * @returns {Object} the interaction current state
     */
    var getState = function getState(interaction){
        var state =  {};
        var response =  interaction.getResponse();

        if(response){
            state.response = response;
        }
        return state;
    };

    var getCustomData = function(interaction, data){
        var pattern = interaction.attr('patternMask'),
            maxWords = parseInt(_parsePattern(pattern,'words')),
            maxLength = parseInt(_parsePattern(pattern, 'chars')),
            expectedLength = parseInt(interaction.attr('expectedLines'),10);
        return _.merge(data || {}, {
            maxWords : (! isNaN(maxWords)) ? maxWords : undefined,
            maxLength : (! isNaN(maxLength)) ? maxLength : undefined,
            attributes : (! isNaN(expectedLength)) ? { expectedLength :  expectedLength * 72} : undefined
        });

    };

    /**
     * Expose the common renderer for the extended text interaction
     * @exports qtiCommonRenderer/renderers/interactions/ExtendedTextInteraction
     */
    return {
        qtiClass : 'extendedTextInteraction',
        template : tpl,
        render : render,
        getContainer : containerHelper.get,
        setResponse : setResponse,
        getResponse : getResponse,
        getData : getCustomData,
        resetResponse : resetResponse,
        destroy : destroy,
        getState : getState,
        setState : setState,

        enable : enable,
        disable : disable,
        clearText : clearText,
        setText : setText
    };
});
