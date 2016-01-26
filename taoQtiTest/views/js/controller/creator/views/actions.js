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
'taoQtiTest/controller/creator/views/property'],
function($, propertyView){
    'use strict';

    var disabledClass = 'disabled';
    var activeClass = 'active';
    var btnOnClass = 'tlb-button-on';

    /**
     * Set up the property view for an element
     * @param {jQueryElement} $container - that contains the property opener
     * @param {String} template - the name of the template to give to the propertyView
     * @param {Object} model - the model to bind
     * @param {PropertyViewCallback} cb - execute at view setup phase
     */
    function properties ($container, template, model, cb) {
        var propView = null;
        $container.find('.property-toggler').on('click', function(e){
            e.preventDefault();
            var $elt = $(this);
            if(!$(this).hasClass(disabledClass)){

                $elt.blur(); //to remove the focus

                if(propView === null){

                    $container.addClass(activeClass);
                    $elt.addClass(btnOnClass);

                    propView = propertyView(template, model);
                    propView.open();

                    propView.onOpen(function(){
                        $container.addClass(activeClass);
                        $elt.addClass(btnOnClass);
                    });
                    propView.onClose(function(){
                        $container.removeClass(activeClass);
                        $elt.removeClass(btnOnClass);
                    });

                    if(typeof cb === 'function'){
                        cb(propView);
                    }
                } else {
                    propView.toggle();
                }
            }
        });
    }


    /**
     * Enable to move an element
     * @param {jQueryElement} $actionContainer - where the mover is
     * @param {String} containerClass - the cssClass of the element container
     * @param {String} elementClass - the cssClass to identify elements
     */
    function move ($actionContainer, containerClass, elementClass) {
        var $element = $actionContainer.parents('.' + elementClass);
        var $container = $element.parents('.' + containerClass );

        //move up an element
        $('.move-up', $actionContainer).click(function(e){
            var $elements, index;

            //prevent default and click during animation
            e.preventDefault();
            if($element.is(':animated')){
                return false;
            }

            //get the position
            $elements = $('.' + elementClass, $container);
            index = $elements.index($element);
            if (index > 0) {
                $element.fadeOut(200, function(){
                    $element
                        .insertBefore($('.' + elementClass + ' :eq(' + (index - 1) + ')', $container))
                        .fadeIn(400, function(){
                            $container.trigger('change');
                        });
                });
            }
        });

        //move down an element
        $('.move-down', $actionContainer).click(function(e){
            var $elements, index;

            //prevent default and click during animation
            e.preventDefault();
            if($element.is(':animated')){
                return false;
            }

            //get the position
            $elements = $('.' + elementClass, $container);
            index = $elements.index($element);
            if (index < ($elements.length - 1) && $elements.length > 1) {
                $element.fadeOut(200, function(){
                    $element
                        .insertAfter($('.' + elementClass + ' :eq(' + (index + 1) + ')', $container))
                        .fadeIn(400, function(){
                            $container.trigger('change');
                        });
                });
            }
        });
    }

    /**
     * Update the movable state of an element
     * @param {jQueryElement} $container - the movable elements (scopped)
     * @param {String} elementClass - the cssClass to identify elements
     * @param {String} actionContainerElt - the element name that contains the actions
     */
    function movable ($container, elementClass, actionContainerElt){
        $container.each(function(){
            var $elt = $(this);
            var $actionContainer = $(actionContainerElt, $elt);

            var index = $container.index($elt);
            var $moveUp = $('.move-up', $actionContainer);
            var $moveDown = $('.move-down', $actionContainer);

            //only one test part, no moving
            if( $container.length === 1 ){
                $moveUp.addClass(disabledClass);
                $moveDown.addClass(disabledClass);

            //testpart is the first, only moving down
            } else if(index === 0) {
                $moveUp.addClass(disabledClass);
                $moveDown.removeClass(disabledClass);

            //testpart is the lasst, only moving up
            } else if ( index >= ($container.length - 1) ) {
                $moveDown.addClass(disabledClass);
                $moveUp.removeClass(disabledClass);

            //or enable moving top/bottom
            } else {
                $moveUp.removeClass(disabledClass);
                $moveDown.removeClass(disabledClass);
            }
         });
    }

    /**
     * Update the removable state of an element
     * @param {jQueryElement} $container - that contains the removable action
     * @param {String} actionContainerElt - the element name that contains the actions
     */
    function removable ($container, actionContainerElt){
        $container.each(function(){
            var $elt = $(this);
            var $actionContainer = $(actionContainerElt, $elt);
            var $delete = $('[data-delete]', $actionContainer);

            if($container.length <= 1){
                $delete.addClass(disabledClass);
            } else {
                $delete.removeClass(disabledClass);
            }
        });
    }

    /**
     * Disable all the actions of the target
     * @param {jQueryElement} $container - that contains the the actions
     * @param {String} actionContainerElt - the element name that contains the actions
     */
    function disable($container, actionContainerElt){
        $container.find(actionContainerElt).find('[data-delete],.move-up,.move-down').addClass(disabledClass);
    }

    /**
     * Enable all the actions of the target
     * @param {jQueryElement} $container - that contains the the actions
     * @param {String} actionContainerElt - the element name that contains the actions
     */
    function enable($container, actionContainerElt){
        $container.find(actionContainerElt).find('[data-delete],.move-up,.move-down').removeClass(disabledClass);
    }

    /**
     * The actions gives you shared behavior for some actions.
     *
     * @exports taoQtiTest/controller/creator/views/actions
     */
    return {
        properties  : properties,
        move        : move,
        removable   : removable,
        movable     : movable,
        disable     : disable,
        enable      : enable
    };
});
