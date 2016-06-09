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

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'taoQtiTest/controller/creator/views/actions',
    'helpers',
    'ckeditor',
    'taoQtiTest/controller/creator/helpers/ckConfigurator'
], function($, _, actions, helpers, ckeditor, ckConfigurator){ // qtiClasses, creatorRenderer, XmlRenderer, simpleParser){
    'use strict';

    //compute ckeditor config only once
    var ckConfig = ckConfigurator.getConfig(ckeditor, 'qtiBlock');

    var filterPlugin = function filterPlugin(plugin){
        return _.contains(['taoqtiimage', 'taoqtimedia','taoqtimaths', 'taoqtiinclude'], plugin);
    };
    ckConfig.plugins = _.reject(ckConfig.plugins.split(','), filterPlugin).join(',');
    ckConfig.extraPlugins = _.reject(ckConfig.extraPlugins.split(','), filterPlugin).join(',');

    /**
     * Set up a rubric block: init action beahviors. Called for each one.
     *
     * @param {jQueryElement} $rubricBlock - the rubricblock to set up
     */
    var setUp = function setUp($rubricBlock, model, data){

        actions.properties($rubricBlock, 'rubricblock', model, propHandler);
        setUpEditor();

        /**
         * Perform some binding once the property view is create
         * @private
         * @param {propView} propView - the view object
         */
        function propHandler(propView){

            rbViews(propView.getView());

            $rubricBlock.parents('.testpart').on('delete', removePropHandler);
            $rubricBlock.parents('.section').on('delete', removePropHandler);
            $rubricBlock.on('delete', removePropHandler);

            function removePropHandler(e){
                if(propView !== null){
                    propView.destroy();
                }
            }
        }

        /**
         * Set up the views select box
         * @private
         * @param {jQuerElement} $propContainer - the element container
         */
        function rbViews($propContainer){
            var $select = $('select', $propContainer);

            $select.select2({
                'width' : '100%'
            }).on("select2-removed", function(e){
                if($select.select2('val').length === 0){
                    $select.select2('val', [1]);
                }
            });

            if($select.select2('val').length === 0){
                $select.select2('val', [1]);
            }
        }

        /**
         * Set up ck editor
         * @private
         */
        function setUpEditor(){
            var editor;

            //we need to synchronize the ck elt with an hidden elt that has data-binding
            var $rubricBlockBinding = $('.rubricblock-binding', $rubricBlock);
            var $rubricBlockContent = $('.rubricblock-content', $rubricBlock);
            var syncRubricBlockContent = _.throttle(function(){
                 $rubricBlockBinding
                    .html($rubricBlockContent.html())
                    .trigger('change');
            }, 100);

            $rubricBlockContent.empty().html($rubricBlockBinding.html());

            editor = ckeditor.inline($rubricBlockContent[0], ckConfig);
            editor.on('change', function(e) {
                syncRubricBlockContent();
            });
        }
    };

    /**
     * The rubriclockView setup RB related components and beahvior
     *
     * @exports taoQtiTest/controller/creator/views/rubricblock
     */
    return {
        setUp : setUp
    };

});
