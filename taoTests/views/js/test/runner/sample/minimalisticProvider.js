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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */
/**
 * @author Sam <sam@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'core/promise',
    'tpl!taoTests/test/runner/sample/layout',
    'taoTests/runner/areaBroker'
], function($, _, Promise, layoutTpl, areaBroker){
    'use strict';

    //Test template


    return {
        name : 'minimalistic',
        init : function init(){
            var self = this;
            var config = this.getConfig();

            //install event based behavior
            this.on('ready', function(){
                this.loadItem('item-0');
            })
            .on('move', function(type){

                var test = this.getTestContext();


                if(type === 'next'){
                   if(test.items[test.current + 1]){
                        self.unloadItem('item-' +test.current);
                        self.loadItem('item-' + (test.current + 1));
                   } else {
                        self.finish();
                   }
                }
                else if(type === 'previous'){

                   if(test.items[test.current - 1]){
                        self.unloadItem('item-' +test.current);
                        self.loadItem('item-' + (test.current - 1));
                   } else {
                        self.loadItem('item-0');
                   }
                }
            });



            //load test data
            return new Promise(function(resolve, reject){

                $.getJSON(config.url).success(function(test){
                    self.setTestContext(_.defaults(test || {}, {
                        items : {},
                        current: 0
                    }));

                    resolve();
                });
            });
        },

        render : function(){

            var config = this.getConfig();
            var context = this.getTestContext();
            var broker = this.getAreaBroker();

            broker.getContainer().find('.title').html('Running Test ' + context.id);

            var $renderTo = config.renderTo || $('body');

            $renderTo.append(broker.getContainer());
        },

        loadItem : function loadItem(itemIndex){
            var self = this;

            var test = this.getTestContext();
            var broker = this.getAreaBroker();
            var $content = broker.getContentArea();

            $content.html('loading');

            return new Promise(function(resolve, reject){

                setTimeout(function(){

                    test.current = parseInt(itemIndex.replace('item-',''), 10);
                    self.setTestContext(test);

                    resolve(test.items[test.current]);
                }, 500);
            });

        },

        renderItem : function renderItem(itemIndex, item){

            var broker = this.getAreaBroker();
            var $content = broker.getContentArea();
            $content.html(
                '<h1>' + item.id + '</h1>' +
                '<div>' + item.content + '</div>'
            );
        },

        finish : function(){

            var broker = this.getAreaBroker();
            var $content = broker.getContentArea();
            $content.html('<h1>Done</h1>');
        },

        loadAreaBroker : function loadAreaBroker(){

            var $layout = $(layoutTpl());
            //set up the areaBroker on a detached node
            return  areaBroker($layout, {
                'content' : $('.content', $layout),
                'toolbox' : $('.toolbox', $layout),
                'navigation' : $('.navigation', $layout),
                'control' : $('.control', $layout),
                'panel' : $('.panel', $layout),
                'header' : $('header', $layout)
            });
        }
    };
});
