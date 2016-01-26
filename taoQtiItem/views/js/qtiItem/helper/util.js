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
 * Common basic util functions
 */
define(['lodash'], function(_){
    'use strict';

    var util = {

        buildSerial : function buildSerial(prefix){
            var id = prefix || '';
            var chars = "abcdefghijklmnopqrstuvwxyz0123456789";
            for(var i = 0; i < 22; i++){
                id += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return id;
        },

        /**
         * Generates an id for a Qti element (the generation is different from identifier)
         * @param {Object} item - the element related item
         * @param {String} prefix - identifier prefix
         * @returns {String} the identifier
         * @throws {TypeError} if there is no item
         */
        buildId : function buildId(item, prefix){
            var id;
            var usedIds;
            var i = 1;
            var suffix = '';
            var exists = false;

            if(!item){
                throw new TypeError('A item is required to generate a unique identifier');
            }

            usedIds   = item.getUsedIds();

            do{
                exists = false;
                id = prefix + suffix;
                if(_.contains(usedIds, id)){
                    exists = true;
                    suffix = '_' + i;
                    i++;
                }
            } while(exists);

            return id;
        },

        /**
         * Generates an identifier for a Qti element
         * @param {Object} item - the element related item
         * @param {String} prefix - identifier prefix
         * @param {Boolean} [useSuffix = true] - add a "_ + index" to the identifier
         * @returns {String} the identifier
         * @throws {TypeError} if there is no item
         */
        buildIdentifier : function buildIdentifier(item, prefix, useSuffix){

            var id;
            var usedIds;
            var suffix = '';
            var i = 1;
            var exists = false;

            if(!item){
                throw new TypeError('A item is required to generate a unique identifier');
            }

            usedIds   = item.getUsedIdentifiers();
            useSuffix = _.isUndefined(useSuffix) ? true : useSuffix;

            if(prefix){
                prefix = prefix.replace(/_[0-9]+$/ig, '_') //detect incremental id of type choice_12, response_3, etc.
                               .replace(/[^a-zA-Z0-9_]/ig, '_')
                               .replace(/(_)+/ig, '_');
                if(useSuffix){
                    suffix = '_' + i;
                }
            } else {
                prefix = this.qtiClass;
                suffix = '_' + i;
            }

            do{
                exists = false;
                id = prefix + suffix;
                if(usedIds[id]){
                    exists = true;
                    suffix = '_' + i;
                    i++;
                }
            } while(exists);

            return id;
        },

        findInCollection : function findInCollection(element, collectionNames, searchedSerial){

            var found = null;

            if(_.isString(collectionNames)){
                collectionNames = [collectionNames];
            }

            if(_.isArray(collectionNames)){

                _.each(collectionNames, function(collectionName){

                    //get collection to search in (resolving case like interaction.choices.0
                    var collection = element;
                    _.each(collectionName.split('.'), function(nameToken){
                        collection = collection[nameToken];
                    });
                    var elt = collection[searchedSerial];

                    if(elt){
                        found = {'parent' : element, 'element' : elt};
                        return false;//break the each loop
                    }

                    //search inside each elements:
                    _.each(collection, function(elt){

                        if(_.isFunction(elt.find)){
                            found = elt.find(searchedSerial);
                            if(found){
                                return false;//break the each loop
                            }
                        }

                    });

                    if(found){
                        return false;//break the each loop
                    }

                });

            }else{

                throw new Error('invalid argument : collectionNames must be an array or a string');
            }

            return found;
        },
        addMarkupNamespace : function addMarkupNamespace(markup, ns){
            if(ns) {
                markup = markup.replace(/<(\/)?([a-z:]+)(\s?)([^><]*)>/g, function($0, $1, $2, $3, $4){
                    if($2.indexOf(':')>0){
                        return $0;
                    }
                    $1 = $1 || '';
                    $3 = $3 || '';
                    return '<'+ $1 + ns + ':'+$2+$3+$4+'>';
                });
                return markup;
            }
            return markup;

        },
        removeMarkupNamespaces : function removeMarkupNamespace(markup){
            return markup.replace(/<(\/)?(\w*):([^>]*)>/g, '<$1$3>');
        },
        getMarkupUsedNamespaces : function getMarkupUsedNamespaces(markup){
            var namespaces = [];
            markup.replace(/<(\/)?(\w*):([^>]*)>/g, function(original, slash, ns, node){
                namespaces.push(ns);
                return '<'+slash+node+'>';
            });
            return _.uniq(namespaces);
        }
    };

    return util;
});
