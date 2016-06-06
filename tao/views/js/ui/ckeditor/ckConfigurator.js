/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
define([
    'jquery',
    'lodash',
    'ckeditor',
    'ui/ckeditor/dtdHandler'
], function($, _, ckeditor, dtdHandler) {
    'use strict';
    /**
     * Cache original config
     */
    var originalConfig = _.cloneDeep(window.CKEDITOR.config);

    var ckConfigurator = (function(){

        /**
         * Toolbar presets that you normally never would need to change, they can however be overridden with options.toolbar.
         * The argument 'toolbarType' determines which toolbar to use
         */
        var toolbarPresets = {
            inline : [{
                    name : 'basicstyles',
                    items : ['Bold', 'Italic', 'Subscript', 'Superscript']
                }, {
                    name : 'insert',
                    items : ['SpecialChar']
                }, {
                    name : 'links',
                    items : ['Link']
                }],
            flow : [{
                    name : 'basicstyles',
                    items : ['Bold', 'Italic', 'Subscript', 'Superscript']
                }, {
                    name : 'insert',
                    items : ['SpecialChar']
                }, {
                    name : 'links',
                    items : ['Link']
                }],
            block : [{
                    name : 'basicstyles',
                    items : ['Bold', 'Italic', 'Subscript', 'Superscript']
                }, {
                    name : 'insert',
                    items : ['Image', 'SpecialChar']
                },
                {
                    name : 'links',
                    items : ['Link']
                },
                '/',
                {
                    name : 'styles',
                    items : ['Format']
                }, {
                    name : 'paragraph',
                    items : ['NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                }],
            extendedText : [{
                    name : 'basicstyles',
                    items : ['Bold', 'Italic', 'Underline','Subscript', 'Superscript']
                },{
                    name : 'insert',
                    items : ['SpecialChar']
                },{
                    name : 'paragraph',
                    items : ['NumberedList', 'BulletedList']
                },{
                    name : 'clipboard',
                    items : ['Cut', 'Copy', 'Paste']
                },{
                    name : 'history',
                    items : ['Undo', 'Redo']
                },{
                    name : 'fontstyles',
                    items : ['TextColor','Font','FontSize']
                }]
        };

        /**
         * defaults for editor configuration
         */
        var ckConfig = {
            disableAutoInline : true,
            entities : false,
            entities_processNumerical : true,
            autoParagraph : false,
            extraPlugins : 'confighelper',
            floatSpaceDockedOffsetY : 0,
            forcePasteAsPlainText : true,
            skin : 'tao',
            language : 'en',
            removePlugins : '',
            linkShowAdvancedTab : false,
            justifyClasses : ['txt-lft', 'txt-ctr', 'txt-rgt', 'txt-jty'],
            linkShowTargetTab : false,
            specialChars : ['!', '&quot;', '#', '$', '%', '&amp;', "'", '(', ')', '*', '+', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', ['&lt;', 'Less than'],
                ['&le;', 'Less than or equal to'], '&asymp;', '=', '&ne;', ['&ge;', 'Greater than or equal to'], ['&gt;', 'Greater than'], '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
                'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', "&euro;", "&lsquo;", "&rsquo;", "&ldquo;", "&rdquo;", "&ndash;", "&mdash;", "&iexcl;",
                "&cent;", "&pound;", "&curren;", "&yen;", "&brvbar;", "&sect;", "&uml;", "&copy;", "&ordf;", "&laquo;", "&not;", "&reg;", "&macr;", "&deg;", "&sup2;", "&sup3;", "&acute;",
                "&micro;", "&para;", "&middot;", "&cedil;", "&sup1;", "&ordm;", "&raquo;", "&frac14;", "&frac12;", "&frac34;", "&iquest;", "&Agrave;", "&Aacute;", "&Acirc;", "&Atilde;",
                "&Auml;", "&Aring;", "&AElig;", "&Ccedil;", "&Egrave;", "&Eacute;", "&Ecirc;", "&Euml;", "&Igrave;", "&Iacute;", "&Icirc;", "&Iuml;", "&ETH;", "&Ntilde;", "&Ograve;", "&Oacute;",
                "&Ocirc;", "&Otilde;", "&Ouml;", "&times;", "&Oslash;", "&Ugrave;", "&Uacute;", "&Ucirc;", "&Uuml;", "&Yacute;", "&THORN;", "&szlig;", "&agrave;", "&aacute;", "&acirc;",
                "&atilde;", "&auml;", "&aring;", "&aelig;", "&ccedil;", "&egrave;", "&eacute;", "&ecirc;", "&euml;", "&igrave;", "&iacute;", "&icirc;", "&iuml;", "&eth;", "&ntilde;",
                "&ograve;", "&oacute;", "&ocirc;", "&otilde;", "&ouml;", "&divide;", "&oslash;", "&ugrave;", "&uacute;", "&ucirc;", "&uuml;", "&yacute;", "&thorn;", "&yuml;", "&OElig;",
                "&oelig;", "&#372;", "&#374", "&#373", "&#375;", "&sbquo;", "&#8219;", "&bdquo;", "&hellip;", "&trade;", "&#9658;", "&bull;", "&rarr;", "&rArr;", "&hArr;", "&diams;","&asymp;"]
        };

        /**
         * Insert positioned plugins at position specified in options.positionedPlugins
         *
         * @param ckConfig
         * @param positionedPlugins
         */
        var _updatePlugins = function(ckConfig, positionedPlugins){

            positionedPlugins =  positionedPlugins || {};

            var itCnt,
                tbCnt = ckConfig.toolbar.length,
                itLen,
                method,
                plugin,
                index,
                separator,
                idxItem,
                numToReplace,
                stringVal,
                stringVals = {},
                i;

            // add positioned plugins to extraPlugins and let CKEDITOR take care of their registration
            ckConfig.extraPlugins = (function(positionedPluginArr, extraPlugins){
                var i = positionedPluginArr.length,
                    extraPluginArr = extraPlugins.split(',');

                while(i--){
                    positionedPluginArr[i] = positionedPluginArr[i].toLowerCase();
                }

                extraPluginArr = _.compact(_.union(extraPluginArr, positionedPluginArr));
                return extraPluginArr.join(',');

            }(_.keys(positionedPlugins), ckConfig.extraPlugins));

            // capture line breaks (/) and such
            // and turn them into a objects temporarily
            for(i = 0; i < tbCnt; i++){
                if(_.isString(ckConfig.toolbar[i])){
                    stringVals[i] = ckConfig.toolbar[i];
                    ckConfig.toolbar[i] = {
                        items : []
                    };
                }
            }

            // add positioned plugins to toolbar
            for(plugin in positionedPlugins){

                method = (function(pluginProps){
                    var i = pluginProps.length;
                    while(i--){
                        if(pluginProps[i].indexOf('insert') === 0 || pluginProps[i] === 'replace'){
                            return pluginProps[i];
                        }
                    }

                    throw 'Missing key insertBefore | insertAfter | replace in positionedPlugins';

                }(_.keys(positionedPlugins[plugin])));


                // the item to insert before | after
                idxItem = positionedPlugins[plugin][method].toLowerCase();
                separator = positionedPlugins[plugin].separator || false;
                index = -1;

                // each button row
                while(tbCnt--){
                    itLen = ckConfig.toolbar[tbCnt].items.length;

                    // each item in row
                    for(itCnt = 0; itCnt < itLen; itCnt++){
                        if(ckConfig.toolbar[tbCnt].items[itCnt].toLowerCase() === idxItem){
                            index = itCnt;
                            break;
                        }
                    }
                    //continue
                    if(index > -1){
                        // ~~ converts bool to number
                        numToReplace = ~~(method === 'replace');
                        if(method === 'insertAfter'){
                            index++;
                        }
                        if(separator){
                            ckConfig.toolbar[tbCnt].items.splice(index, numToReplace, '-');
                            index++;
                        }
                        ckConfig.toolbar[tbCnt].items.splice(index, numToReplace, plugin);
                        break;
                    }
                }
                // reset tbCnt
                tbCnt = ckConfig.toolbar.length;
            }


            // re-add toolbar line breaks
            for(stringVal in stringVals){
                ckConfig.toolbar[stringVal] = stringVals[stringVal];
            }

        };

        /**
         * Generate a configuration object for CKEDITOR
         *
         * @param editor instance of ckeditor
         * @param toolbarType block | inline | flow | qtiBlock | qtiInline | qtiFlow | reset to get back to normal
         * @param {Object} [options] - is based on the CKEDITOR config object with some additional sugar
         *        Note that it's here you need to add parameters for the resource manager.
         *        Some options are not covered in http://docs.ckeditor.com/#!/api/CKEDITOR.config
         * @param [options.dtdOverrides] - @see dtdOverrides which pre-defines them
         * @param {Object} [options.positionedPlugins] - @see ckConfig.positionedPlugins
         * @param {Boolean} [options.qtiImage] - enables the qtiImage plugin
         * @param {Boolean} [options.qtiInclude] - enables the qtiInclude plugin
         * @param {Boolean} [options.underline] - enables the underline plugin
         * @param {Boolean} [options.mathJax] - enables the mathJax plugin
         *
         * @see http://docs.ckeditor.com/#!/api/CKEDITOR.config
         */
        var getConfig = function(editor, toolbarType, options){

            if(toolbarType === 'reset'){
                return originalConfig;
            }

            options = options || {};

            options.resourcemgr = options.resourcemgr || {};

            var toolbar,
                toolbars = _.clone(toolbarPresets, true),
                config,
                dtdMode = options.dtdMode || 'html';

            // This is different from CKEDITOR.config.extraPlugins since it also allows to position the button
            // Valid positioning keys are insertAfter | insertBefore | replace followed by the button name, e.g. 'Anchor'
            // separator bool, defaults to false
            var positionedPlugins = {};

            // modify DTD to either comply with QTI or XHTML
            if(dtdMode === 'qti' || toolbarType.indexOf('qti') === 0){
                toolbarType = toolbarType.slice(3).toLowerCase();
                ckConfig.allowedContent = true;
                ckConfig.autoParagraph = false;
                dtdMode = 'qti';
            }

            // modify plugins - this will change the toolbar too
            // this would add the qti plugins in positionedPlugins
            if (dtdMode === 'qti') {
                if (options.qtiImage) {
                    positionedPlugins.TaoQtiImage = {insertAfter: 'SpecialChar'};
                }
                if (options.qtiInclude) {
                    positionedPlugins.TaoQtiInclude = {insertAfter: 'SpecialChar'};
                }
                if (options.underline) {
                    positionedPlugins.TaoUnderline = {insertAfter: 'Italic'};
                }
                if (options.mathJax) {
                    positionedPlugins.TaoQtiMaths = {insertAfter: 'SpecialChar'};
                }

            }

            // if there is a toolbar in the options add it to the set
            if(options.toolbar){
                toolbars[toolbarType] = _.clone(options.toolbar);
            }

            // add toolbars to config
            for(toolbar in toolbars){
                if(toolbars.hasOwnProperty(toolbar)){
                    ckConfig['toolbar_' + toolbar] = toolbars[toolbar];
                }
            }

            // add the toolbar
            if(typeof toolbars[toolbarType] !== 'undefined'){
                ckConfig.toolbar = toolbars[toolbarType];
            }

            // ensures positionedPlugins has the right format
            if(typeof options.positionedPlugins !== 'undefined'){
                options.positionedPlugins = {};
            }

            // set options.positionedPlugins to false to prevent the class from using them at all
            if(false !== options.positionedPlugins){
                // this would add positionedPlugins (e.g. the media manager)
                positionedPlugins = _.assign(positionedPlugins, _.clone(options.positionedPlugins));
                _updatePlugins(ckConfig, positionedPlugins);
            }

            // forward the options to ckConfig, exclude local options
            config = _.assign({}, _.cloneDeep(originalConfig), ckConfig, _.omit(options, [
                'qtiImage', 'qtiInclude', 'underline', 'mathJax', 'toolbar', 'positionedPlugins'
            ]));

            // debugger: has this config been used?
            //config.aaaConfigurationHasBeenLoadedFromConfigurator = true;

            // toggle global DTD
            // I know that this is rather ugly
            editor.on('focus', function(e){
                dtdHandler.setMode(dtdMode);
                window.CKEDITOR.dtd = dtdHandler.getDtd();
                // should be 1 on html, undefined on qti
                // console.log(CKEDITOR.dtd.pre.img)
            });

            // remove title 'Rich Text Editor, instance n' that CKE sets by default
            // ref: http://tinyurl.com/keedruc
            editor.on('instanceReady', function(e){
                $(e.editor.element.$).removeAttr('title');
            });

            // This fixes bug #2855. Unfortunately this can be done on the global object only, not on the instance
            window.CKEDITOR.on('dialogDefinition', function(e) {
                if(e.data.name !== 'link') {
                    return;
                }
                var linkTypes = e.data.definition.getContents('info').get('linkType')['items'],
                    i = linkTypes.length,
                    wanted;

                while(i--) {
                    if(linkTypes[i][1] !== 'anchor') {
                        wanted = i;
                        continue;
                    }
                }

                linkTypes.splice(wanted + 1, 1);
                return;
            });


            return config;
        };

        return {
            getConfig : getConfig
        };

    }());

    return ckConfigurator;
});

