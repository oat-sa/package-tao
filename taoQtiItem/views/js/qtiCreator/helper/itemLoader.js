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
    'helpers',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiCreator/model/Item',
    'taoQtiItem/qtiCreator/model/qtiClasses'
], function($, helpers, Loader, Item, qtiClasses){
    "use strict";
    var _generateIdentifier = function(uri){
        var pos = uri.lastIndexOf('#');
        return uri.substr(pos + 1);
    };

    var creatorLoader = {
        loadItem : function(config, callback){

            if(config.uri){
                $.ajax({
                    url : helpers._url('getItemData', 'QtiCreator', 'taoQtiItem'),
                    dataType : 'json',
                    data : {
                        uri : config.uri
                    }
                }).done(function(data){

                    if(data.itemData && data.itemData.qtiClass === 'assessmentItem'){

                        var loader = new Loader().setClassesLocation(qtiClasses),
                            itemData = data.itemData;

                        loader.loadItemData(itemData, function(item){

                            //hack to fix #2652
                            if(item.isEmpty()){
                                item.body('');
                            }

                            callback(item, this.getLoadedClasses());
                        });
                    }else{
                        
                        var item = new Item().id(_generateIdentifier(config.uri)).attr('title', config.label);
                        var outcome = item.createOutcomeDeclaration({
                            cardinality : 'single',
                            baseType : 'float'
                        });
                        outcome.buildIdentifier('SCORE', false);

                        item.createResponseProcessing();

                        //set default namespaces
                        item.setNamespaces({
                            '' : 'http://www.imsglobal.org/xsd/imsqti_v2p1',
                            'xsi' : 'http://www.w3.org/2001/XMLSchema-instance',
                            'm' :'http://www.w3.org/1998/Math/MathML'
                        });//note : always add math element : since it has become difficult to know when a math element has been added to the item
                        
                        //set default schema locations
                        item.setSchemaLocations({
                            'http://www.imsglobal.org/xsd/imsqti_v2p1' : 'http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd'
                        });
                        
                        //tag the item as a new one
                        item.data('new', true);
                        
                        callback(item);
                    }

                });
            }
        }
    };

    return creatorLoader;
});
