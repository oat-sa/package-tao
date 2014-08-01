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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['jquery', 'i18n', 'helpers', 'uiBootstrap', 'module'], function ($, __, helpers, uiBootstrap, module) {
        
        var $tabs = uiBootstrap.tabs;
        var authoringIndex = helpers.getTabIndexByName('items_authoring');
        var previewIndex = helpers.getTabIndexByName('items_preview');

        function setAuthoringItemLabel(label){
            var authoringLabel = (label) ? __('Authoring') + ': ' + label : __('Authoring');
            var previewLabel = (label) ?__('Preview') + ': ' + label : __('Preview') ;
            $('a#items_authoring').text(authoringLabel).attr('title', authoringLabel);
            $('a#items_preview').text(previewLabel).attr('title', previewLabel);
        }
        
        return {
            start : function(){
                var conf = module.config();

                if(conf.uri && conf.classUri){
                    if(conf.isAuthoringEnabled === true && conf.action !== 'authoring'){
                        $tabs.tabs('url', authoringIndex, conf.authoringUrl);
                        $tabs.tabs('enable', authoringIndex);
                    }
                    if(conf.isPreviewEnabled === true && conf.action !== 'preview'){
                        $tabs.tabs('url', previewIndex, conf.previewUrl);
                        $tabs.tabs('enable', previewIndex);
                    }
                    if(conf.label){
                        setAuthoringItemLabel(conf.label);
                    }
                } else {
                     setAuthoringItemLabel();
                    if(conf.action !== 'authoring'){
                        $tabs.tabs('disable', authoringIndex);
                    }
                    if(conf.action !== 'preview'){
                        $tabs.tabs('disable', previewIndex);
                    }
                }
                
                if(conf.reload){
                    uiBootstrap.initTrees();
                }
                if(conf.message){
                    helpers.createMessage(conf.message);
                }
            }
        };
});


