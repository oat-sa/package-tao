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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function($, _, Element, rendererConfig){
    'use strict';

    var Container = Element.extend({
        qtiClass : '_container',
        init : function(body){
            this._super();//generate serial, attributes array always empty
            if(body && typeof body !== 'string'){
                throw 'the body of a container must be a string';
            }
            this.bdy = body || '';
            this.elements = {};
        },
        body : function(body){
            if(typeof body === 'undefined'){
                return this.bdy;
            }else{
                if(typeof body === 'string'){
                    this.bdy = body;
                    $(document).trigger('containerBodyChange', {
                        body : body,
                        container : this
                    });
                }else{
                    throw 'body must be a string';
                }
            }
        },
        setElements : function(elements, body){
            var returnValue = false;

            for(var i in elements){
                var elt = elements[i];
                if(elt instanceof Element){

                    body = body || this.bdy;
                    if(body.indexOf(elt.placeholder()) === -1){
                        body += elt.placeholder();//append the element if no placeholder found
                    }

                    elt.setRelatedItem(this.getRelatedItem() || null);
                    this.elements[elt.getSerial()] = elt;
                    $(document).trigger('containerElementAdded', {
                        element : elt,
                        container : this
                    });

                    returnValue = true;
                }else{
                    returnValue = false;
                    throw 'expected a qti element';
                }
            }

            this.body(body);

            return returnValue;
        },
        setElement : function(element, body){
            return this.setElements([element], body);
        },
        removeElement : function(element){
            var serial = '';
            if(typeof(element) === 'string'){
                serial = element;
            }else if(element instanceof Element){
                serial = element.getSerial();
            }
            delete this.elements[serial];
            this.body(this.body().replace('{{' + serial + '}}', ''));
            return this;
        },
        getElements : function(qtiClass){
            var elts = {};
            if(typeof(qtiClass) === 'string'){
                for(var serial in this.elements){
                    if(Element.isA(this.elements[serial], qtiClass)){
                        elts[serial] = this.elements[serial];
                    }
                }
            }else{
                elts = _.clone(this.elements);
            }
            return elts;
        },
        getElement : function(serial){
            return this.elements[serial] ? this.elements[serial] : null;
        },
        getComposingElements : function(){
            var elements = this.getElements();
            var elts = {};
            for(var serial in elements){
                elts[serial] = elements[serial];//pass individual object by ref, instead of the whole list(object)
                elts = _.extend(elts, elements[serial].getComposingElements());
            }
            return elts;
        },
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                elementsData = [],
                tpl = this.body();

            for(var serial in this.elements){
                var elt = this.elements[serial];
                if(typeof elt.render === 'function'){
                    if(elt.qtiClass === '_container'){
                        //@todo : container rendering merging, to be tested
                        tpl = tpl.replace(elt.placeholder(), elt.render(renderer));
                    }else{
                        tpl = tpl.replace(elt.placeholder(), '{{{' + serial + '}}}');
                        elementsData[serial] = elt.render(renderer);
                    }
                }else{
                    throw 'render() is not defined for the qti element: ' + serial;
                }
            }

            if(renderer.isRenderer){
                return this._super({
                    body : renderer.renderDirect(tpl, elementsData),
                    contentModel : this.contentModel || 'flow'
                }, renderer, args.placeholder);
            }else{
                throw 'invalid qti renderer for qti container';
            }
        },
        postRender : function(data, altClassName, renderer){
            renderer = renderer || this.getRenderer();

            var res = _(this.elements)
                .filter( function(elt){
                    return typeof elt.postRender === 'function';
                })
                .map(function(elt){
                    return elt.postRender(data, '', renderer);
                })
                .flatten(true)
                .value()
                .concat(this._super(data, altClassName, renderer));
            return res;
        },
        toArray : function(){
            var arr = {
                serial : this.serial,
                body : this.bdy,
                elements : {}
            };

            for(var serial in this.elements){
                arr.elements[serial] = this.elements[serial].toArray();
            }

            return arr;
        },
        find : function(serial, parent){

            var found = null;

            if(this.elements[serial]){

                found = {parent : parent || this, element : this.elements[serial], location : 'body'};

            }else{

                _.each(this.elements, function(elt){

                    found = elt.find(serial);
                    if(found){
                        return false;//break loop
                    }
                });
            }

            return found;
        },
        isEmpty : function(){
            return !this.bdy;
        }
    });

    return Container;
});
