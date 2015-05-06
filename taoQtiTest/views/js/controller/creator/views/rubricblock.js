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
 * FIXME There is a strong dependency to the QtiItem extension, due to the reuse of the QTI Editor for the rubricblock. 
 */
define([
    'jquery',
    'lodash',
    'taoQtiTest/controller/creator/views/actions',
    'taoQtiItem/qtiCreator/model/qtiClasses',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer',
    'taoQtiItem/qtiCreator/helper/simpleParser',
    'helpers'
], function($, _, actions, qtiClasses, creatorRenderer, XmlRenderer, simpleParser, helpers){
    'use strict';

    /**
     * Set up a rubric block: init action beahviors. Called for each one.
     *
     * @param {jQueryElement} $rubricBlock - the rubricblock to set up
     */
    var setUp = function setUp($rubricBlock, model, data){
        
        actions.properties($rubricBlock, 'rubricblock', model, propHandler);
        setUpEditor();

        $('formatting-toggler', $rubricBlock).click(function(){

            $rubricBlock.find('.cke_editable').focus().click();
        });

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

        function setUpEditor(){

            var mathNs = 'm';//for 'http://www.w3.org/1998/Math/MathML'
            var $rubricBlockBinding = $('.rubricblock-binding', $rubricBlock);
            var $rubricBlockContent = $('.rubricblock-content', $rubricBlock);
            var $editorForm = $('<div class="rubricblock-formatting-props props clearfix">');
               //uncomment to manage images
               $editorForm.appendTo('.test-creator-props').hide();
            var fakeXml = '<rubricBlock>' + $rubricBlockBinding.html() + '</rubrickBlock>';

            var xmlRenderer = new XmlRenderer({shuffleChoices : false}).load();

            //parse xml
            simpleParser.parse(fakeXml, {
                ns : {
                    math : mathNs
                },
                model : qtiClasses,
                loaded : function(rubricBlock){

                    var uri = data.uri, lang = 'en-US';
                    
                    creatorRenderer.setOptions({
                        uri : uri,
                        lang : lang,
                        baseUrl : helpers._url('download', 'TestContent', 'taoQtiTest') + '?uri=' + encodeURIComponent(uri) + '&lang=' + lang + '&path=',
                        interactionOptionForm : $(),
                        choiceOptionForm : $(),
                        responseOptionForm : $(),
                        bodyElementOptionForm : $editorForm,
                        itemOptionForm : $(),
                        textOptionForm : $(),
                        mediaManager : {
                            appendContainer : '#test-creator',
                            browseUrl : helpers._url('files', 'TestContent', 'taoQtiTest'),
                            uploadUrl : helpers._url('upload', 'TestContent', 'taoQtiTest'),
                            deleteUrl : helpers._url('delete', 'TestContent', 'taoQtiTest'),
                            downloadUrl : helpers._url('download', 'TestContent', 'taoQtiTest')
                        }
                    });

                    creatorRenderer.get().load(function(){

                        var syncRubricBlockContent = _.throttle(function(){
                             $rubricBlockBinding
                                .html($(rubricBlock.render(xmlRenderer)).html())
                                .trigger('change');
                        }, 500);

                        rubricBlock.setRenderer(this);
                        $rubricBlockContent.html(rubricBlock.render());
                        var widget = rubricBlock.postRender({
                            ready : function(){
                                this.changeState('active');
                            }
                        });

                        //disable some elements that are not yet ready or not useful   
                        $('.mini-tlb [data-role="delete"]', $rubricBlockContent).remove();
                        $rubricBlockContent.on('editorready', function(){
                            //comment to manage images
                            $('.cke_button__taoqtiimage').remove();
                            $('.cke_button__taoqtimaths').remove();
                        });

                        widget.on('containerBodyChange', function(data){
                            if(data.container.serial === rubricBlock.getBody().serial){
                                syncRubricBlockContent();
                            }

                        }, true);

                    }, this.getLoadedClasses());
                }
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
