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
define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/model/helper/event',
    'taoQtiItem/qtiCreator/model/helper/invalidator',
    'util/url'
], function(_, $, Element, event, invalidator, url){
    "use strict";

    var _removeSelf = function(element){

        var removed = false,
            related = element.getRelatedItem();

        if(related){

            var found = related.find(element.getSerial());

            if(found){

                var parent = found.parent;
                if(Element.isA(parent, 'interaction')){

                    if(element.qtiClass === 'gapImg'){
                        parent.removeGapImg(element);
                    }else if(Element.isA(element, 'choice')){
                        parent.removeChoice(element);
                    }
                    removed = true;

                }else if(found.location === 'body' && _.isFunction(parent.initContainer)){

                    if(_.isFunction(element.beforeRemove)){
                        element.beforeRemove();
                    }

                    parent.getBody().removeElement(element);
                    removed = true;

                }else if(Element.isA(parent, '_container')){

                    if(_.isFunction(element.beforeRemove)){
                        element.beforeRemove();
                    }

                    parent.removeElement(element);
                    removed = true;
                }

                if(removed){
                    //mark it instantly as removed in case its is being used somewhere else
                    element.data('removed', true);
                    invalidator.completelyValid(element);
                    event.deleted(element, parent);
                }
            }
        }else{
            throw 'no related item found';
        }

        return removed;
    };

    var _removeElement = function(element, containerPropName, eltToBeRemoved){

        if(element[containerPropName]){

            var targetSerial = '',
                targetElt;

            if(typeof (eltToBeRemoved) === 'string'){
                targetSerial = eltToBeRemoved;
                targetElt = Element.getElementBySerial[targetSerial];
            }else if(eltToBeRemoved instanceof Element){
                targetSerial = eltToBeRemoved.getSerial();
                targetElt = eltToBeRemoved;
            }

            if(targetSerial){
                invalidator.completelyValid(targetElt);
                delete element[containerPropName][targetSerial];
                Element.unsetElement(targetSerial);
            }
        }

        return element;
    };

    var methods = {
        init : function(serial, attributes){

            //init call in the format init(attributes)
            if(typeof (serial) === 'object'){
                attributes = serial;
                serial = '';
            }

            var attr = {};

            if(_.isFunction(this.getDefaultAttributes)){
                _.extend(attr, this.getDefaultAttributes());
            }
            _.extend(attr, attributes);

            this._super(serial, attr);
        },
        attr : function(key, value){
            var ret = this._super(key, value);
            if(key !== undefined && value !== undefined){
                $(document).trigger('attributeChange.qti-widget', {'element' : this, 'key' : key, 'value' : value});
            }
            return url.encodeAsXmlAttr(ret);
        },
        removeAttr : function(key){
            var ret = this._super(key);
            $(document).trigger('attributeChange.qti-widget', {'element' : this, 'key' : key, 'value' : null});
            return ret;
        },
        remove : function(){
            if(arguments.length === 0){
                return _removeSelf(this);
            }else if(arguments.length === 2){
                return _removeElement(this, arguments[0], arguments[1]);
            }else{
                throw 'invalid number of argument given';
            }
        }
    };

    return methods;
});