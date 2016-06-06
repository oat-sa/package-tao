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
    'uri',
    'i18n',
    'taoQtiTest/controller/creator/views/actions',
    'taoQtiTest/controller/creator/views/itemref',
    'taoQtiTest/controller/creator/views/rubricblock',
    'taoQtiTest/controller/creator/templates/index',
    'taoQtiTest/controller/creator/helpers/qtiTest',
    'taoQtiTest/controller/creator/helpers/sectionCategory'
],
function($, _, uri, __, actions, itemRefView, rubricBlockView, templates, qtiTestHelper, sectionCategory){
    'use strict';

   /**
    * Set up a section: init action beahviors. Called for each section.
    *
    * @param {jQueryElement} $sectuin - the section to set up
    * @param {Object} model - the data model to bind to the test part
    * @param {Object} [data] - additionnal data used by the setup
    * @param {Array} [data.identifiers] - the locked identifiers
    */
   var setUp = function setUp ($section, model, data){

        var $actionContainer = $('h2', $section);

        actions.properties($actionContainer, 'section', model, propHandler);
        actions.move($actionContainer, 'sections', 'section');
        itemRefs();
        acceptItemRefs();
        rubricBlocks();
        addRubricBlock();

        //trigger for the case the section is added an a selection is ongoing


        /**
         *  Perform some binding once the property view is create
         *  @param {propView} propView - the view object
         */
        function propHandler (propView) {

            var $view = propView.getView();
            
            //enable/disable selection
            var $selectionSwitcher = $('[name=section-enable-selection]', $view);
            var $selectionSelect = $('[name=section-select]', $view);
            var $selectionWithRep = $('[name=section-with-replacement]', $view);

            var switchSelection = function switchSelection(){
                 if($selectionSwitcher.prop('checked') === true){
                    $selectionSelect.incrementer('enable');
                    $selectionWithRep.removeClass('disabled');
                 } else {
                    $selectionSelect.incrementer('disable');
                    $selectionWithRep.addClass('disabled');
                 }
            };
            $selectionSwitcher.on('change', switchSelection);
            $selectionSwitcher.on('change', function updateModel(){
                 if(!$selectionSwitcher.prop('checked')){
                     $selectionSelect.val(0);
                     $selectionWithRep.prop('checked', false);
                     delete model.selection;
                 }
            });

            $selectionSwitcher.prop('checked', !!model.selection).trigger('change');

            //listen for databinder change to update the test part title
            var $title =  $('[data-bind=title]', $section);
            $view.on('change.binder', function(e, model){
                if(e.namespace === 'binder' && model['qti-type'] === 'assessmentSection'){
                    $title.text(model.title);
                }
            });

            $section.parents('.testpart').on('deleted.deleter', removePropHandler);
            $section.on('deleted.deleter', removePropHandler);
            
            //section level category configuration
            categoriesProperty($view);
            
            function removePropHandler(){
                if(propView !== null){
                    propView.destroy();
                }
            }
        }
        
        /**
         * Set up the item refs that already belongs to the section
         * @private
         */
        function itemRefs(){

            if(!model.sectionParts){
                model.sectionParts = [];
            }
            $('.itemref', $section).each(function(){
                var $itemRef = $(this);
                var index = $itemRef.data('bind-index');
                if(!model.sectionParts[index]){
                    model.sectionParts[index] = {};
                }

                itemRefView.setUp($itemRef, model.sectionParts[index]);
                $itemRef.find('.title').text(
                    data.labels[uri.encode($itemRef.data('uri'))]
                );
            });
        }

        /**
         * Make the section to accept the selected items
         * @private
         */
        function acceptItemRefs(){
            var $selected;
            var $items     = $('.test-creator-items');

             //the item selector trigger a select event
             $items.on('itemselect.creator', function(e){
                var selection = Array.prototype.slice.call(arguments, 1);
                var $placeholder = $('.itemref-placeholder', $section);
                var $placeholders = $('.itemref-placeholder');
                
                if(selection.length > 0){
                    $placeholder.show().off('click').on('click', function(e){
                        
                        //prepare the item data 
                        var categories, 
                            defaultItemData = {};
                            
                        if(model.itemSessionControl && !_.isUndefined(model.itemSessionControl.maxAttempts)){
                            
                            //for a matter of consistency, the itemRef will "inherit" the itemSessionControl configuration from its parent section
                            defaultItemData.itemSessionControl = _.clone(model.itemSessionControl);
                        }
                        
                        //the itemRef should also "inherit" the categories set at the item level
                        categories = sectionCategory.getCategories(model);
                        defaultItemData.categories = categories.propagated;
                            
                        _.forEach(selection, function(item){
                            var $item = $(item);

                            addItemRef($('.itemrefs', $section), undefined, _.defaults({
                                href        : uri.decode($item.data('uri')),
                                label       : $.trim($item.clone().children().remove().end().text()),
                                'qti-type'  : 'assessmentItemRef'
                            }, defaultItemData));
                        });

                        //reset the current selection
                        $('.ui-selected', $items).removeClass('ui-selected').removeClass('selected');
                        $placeholders.hide().off('click');
                    });
                } else {
                    $placeholders.hide().off('click');
                }
             });


            //we listen the event not from the adder but  from the data binder to be sure the model is up to date
            $(document)
              .off('add.binder', '#' + $section.attr('id') + ' .itemrefs')
              .on('add.binder', '#' + $section.attr('id') + ' .itemrefs', function(e, $itemRef){
                if(e.namespace === 'binder' && $itemRef.hasClass('itemref')){
                    var index = $itemRef.data('bind-index');

                    //initialize the new item ref
                    itemRefView.setUp($itemRef, model.sectionParts[index]);
                }
            });

            //on set up, if there is a selection ongoing, we trigger the event
            $selected = $('.selected', $items);
            if($selected.length > 0){
                $items.trigger('itemselect.creator', $selected);
            }

        }

        /**
         * Add a new item ref to the section
         * @param {jQueryElement} $refList - the element to add the item to
         * @param {Number} [index] - the position of the item to add
         * @param {Object} [itemData] - the data to bind to the new item ref
         */
        function addItemRef($refList, index, itemData){
           var $itemRef;
           var $items = $refList.children('li');
           index = index || $items.length;
           itemData.identifier = qtiTestHelper.getIdentifier('item', data.identifiers);
           itemData.index = index + 1;
           $itemRef = $(templates.itemref(itemData));
           if(index > 0){
               $itemRef.insertAfter($items.eq(index - 1));
           } else {
               $itemRef.appendTo($refList);
           }
           $refList.trigger('add', [$itemRef, itemData]);
        }


        /**
         * Set up the rubric blocks that already belongs to the section
         * @private
         */
        function rubricBlocks () {
            if(!model.rubricBlocks){
                model.rubricBlocks = [];
            }
            $('.rubricblock', $section).each(function(){
                var $rubricBlock = $(this);
                var index = $rubricBlock.data('bind-index');
                if(!model.rubricBlocks[index]){
                    model.rubricBlocks[index] = {};
                }

                rubricBlockView.setUp($rubricBlock, model.rubricBlocks[index], data);
            });

            //opens the rubric blocks section if they are there.
            if(model.rubricBlocks.length > 0){
                $('.rub-toggler', $section).trigger('click');
            }
        }

        /**
         * Enable to add new rubrick block
         * @private
         */
        function addRubricBlock () {

            $('.rublock-adder', $section).adder({
                target: $('.rubricblocks', $section),
                content : templates.rubricblock,
                templateData : function(cb){
                    cb({
                        'qti-type' : 'rubricBlock',
                        index  : $('.rubricblock', $section).length,
                        content : [],
                        views : [1]
                    });
                }
            });

            //we listen the event not from the adder but  from the data binder to be sure the model is up to date
            $(document).on('add.binder', '#' + $section.attr('id') + ' .rubricblocks', function(e, $rubricBlock){
                if(e.namespace === 'binder' && $rubricBlock.hasClass('rubricblock')){
                    var index = $rubricBlock.data('bind-index');
                    $('.rubricblock-binding', $rubricBlock).html('<p>&nbsp;</p>');
                    rubricBlockView.setUp($rubricBlock, model.rubricBlocks[index], data);
                }
            });
        }
        
        /**
         * Set up the category property
         * @private
         * @param {jQueryElement} $view - the $view object containing the $select
         */
        function categoriesProperty($view){
            
            var $select = $('[name=section-category]', $view);
            $select.select2({
                width: '100%',
                tags : [],
                multiple : true,
                tokenSeparators: [",", " ", ";"],
                formatNoMatches : function(){
                    return __('Enter a category');
                },
                maximumInputLength : 32
            }).on('change', function(e){
                setCategories(e.val);
            });
            
            initCategories();
            $view.on('propopen.propview', function(){
                initCategories();
            });
            
            /**
             * Start the categories editing
             * @private
             */
            function initCategories(){
                
                var categories = sectionCategory.getCategories(model);
                
                //set categories found in the model in the select2 input
                $select.select2('val', categories.all);
                
                //color partial categories
                $select.siblings('.select2-container').find('.select2-search-choice').each(function(){
                   var $li = $(this);
                   var content = $li.find('div').text();
                   if(_.indexOf(categories.partial, content) >= 0){
                       $li.addClass('partial');
                   }
                });
            }
            
            /**
             * save the categories into the model
             * @private
             */
            function setCategories(categories){
                sectionCategory.setCategories(model, categories);
            }
            
        }
   };

   /**
    * Listen for state changes to enable/disable . Called globally.
    */
   var listenActionState =  function listenActionState (){

        var $sections;
        var $actionContainer;

        $('.sections').each(function(){
            $sections = $('.section', $(this));

            actions.removable($sections, 'h2');
            actions.movable($sections, 'section', 'h2');
        });

        $(document)
        .on('delete', function(e){
            var $parent;
            var $target = $(e.target);
            if($target.hasClass('section')){
                $parent = $target.parents('.sections');
                actions.disable($parent.find('.section'), 'h2');
           }
        })
        .on('add change undo.deleter deleted.deleter', function(e){
            var $target = $(e.target);
            if($target.hasClass('section') || $target.hasClass('sections')){
                $sections = $('.section', $target.hasClass('sections') ? $target : $target.parents('.sections'));
                actions.removable($sections, 'h2');
                actions.movable($sections, 'section', 'h2');
            }
        })
        .on('open.toggler', '.rub-toggler', function(e){
            if(e.namespace === 'toggler'){
               $(this).parents('h2').addClass('active');
            }
        })
        .on('close.toggler', '.rub-toggler', function(e){
            if(e.namespace === 'toggler'){
               $(this).parents('h2').removeClass('active');
            }
        });
   };


   /**
     * The sectionView setup section related components and beahvior
     *
     * @exports taoQtiTest/controller/creator/views/section
     */
    return {
        setUp : setUp,
        listenActionState: listenActionState
   };
});
