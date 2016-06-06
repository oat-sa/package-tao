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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

/**
 * Helpers for portable elements
 *
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiItem/helper/util'
], function(_, $, util){
    'use strict';

    /**
     * Add ns directory to a relative path (a relative path only)
     *
     * @param {String} typeIdentifier
     * @param {String} file
     * @returns {String}
     */
    function _addNsDir(typeIdentifier, file){
        if(file.match(/^\./)){
            return typeIdentifier + '/' + file.replace(/^\.\//, '');
        }else{
            return file;
        }
    }

    /**
     * Add namespace directory to a file or an array of file
     *
     * @param {String} typeIdentifier
     * @param {String|Array} file - a file path or an array of file path
     * @returns {String}
     */
    function addNamespaceDirectory(typeIdentifier, file){
        if(_.isString(file)){
            return _addNsDir(typeIdentifier, file);
        }else if(_.isArray(file)){
            return _.map(file, function(f){
                return _addNsDir(typeIdentifier, f);
            });
        }
    }

    /**
     * Get common methods to augment a portableElement implementation
     *
     * @param {object} registry
     * @returns {object}
     */
    function getDefaultMethods(registry){

        return {
            getDefaultAttributes : function(){
                return {};
            },
            getDefaultProperties : function(){

                var creator = registry.getCreator(this.typeIdentifier);
                if(_.isFunction(creator.getDefaultProperties)){
                    return creator.getDefaultProperties(this);
                }else{
                    return {};
                }
            },
            afterCreate : function(){

                var typeId = this.typeIdentifier,
                    creator = registry.getCreator(typeId),
                    manifest = registry.getManifest(typeId),
                    item = this.getRelatedItem(),
                    response;

                //add required resource
                //@todo need afterCreate() to return a promise
                var _this = this;
                registry.addRequiredResources(typeId, item.data('uri'), function(res){
                    if(res.success){
                        $(document).trigger('resourceadded.qti-creator', [typeId, res.resources, _this]);
                    }else{
                        throw 'resource addition failed';
                    }
                });

                //set default markup (for initial rendering)
                creator.getMarkupTemplate();

                //set pci props
                this.properties = creator.getDefaultProperties();

                //set hook entry point
                this.entryPoint = addNamespaceDirectory(typeId, manifest.entryPoint);

                //set libs
                if(_.isArray(manifest.libraries)){
                    this.libraries = addNamespaceDirectory(typeId, manifest.libraries);
                }

                if(_.isArray(manifest.css)){
                    this.css = addNamespaceDirectory(typeId, manifest.css);
                    _.each(this.css, function(css){
                        if(!item.stylesheetExists(css)){
                            item.createStyleSheet(css);
                        }
                    });
                }

                //@todo fix this !
                if(manifest.response){//for custom interaciton only
                    //create response
                    response = this.createResponse({
                        cardinality : manifest.response.cardinality
                    });
                    
                    //the base type is optional
                    if(manifest.response.baseType){
                        response.attr('baseType', manifest.response.baseType);
                    }
                } else {
                    //the attribute is mendatory for info control
                    this.attr('title', manifest.label);

                    //we ensure the info control has an identifier
                    if(!this.attr('id')){
                        this.attr('id', util.buildId(this.getRelatedItem(), typeId));
                    }
                }

                //set markup
                this.markup = this.renderMarkup();

                //set pci namespace to item
                this.getNamespace();

                //after create
                if(_.isFunction(creator.afterCreate)){
                    return creator.afterCreate(this);
                }
            },
            renderMarkup : function(){

                var creator = registry.getCreator(this.typeIdentifier),
                    markupTpl = creator.getMarkupTemplate(),
                    markupData = this.getDefaultMarkupTemplateData();

                if(_.isFunction(creator.getMarkupData)){
                    //extends the default data with the custom one
                    markupData = creator.getMarkupData(this, markupData);
                }

                return markupTpl(markupData);
            },
            updateMarkup : function(){
                this.markup = this.renderMarkup();
            }
        };
    }

    return {
        getDefaultMethods : getDefaultMethods
    };
});
