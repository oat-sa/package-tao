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
* Copyright (c) 2015 (original work) Open Assessment Technologies SA;
*
*/

define([
    'IMSGlobal/jquery_2_1_1',
    'OAT/lodash',
    'OAT/interact',
    'OAT/interact-rotate'
], function(
    $,
    _,
    interact,
    rotator
){

    'use strict';

    interact.maxInteractions(Infinity);

    function setupControls($container, $controls) {

        var hasFocus = false;

        $controls.on('mousedown', function() {
            $controls.not(this).addClass('lurking');
            $(this).addClass('active');
        });
        $container.on('mouseup mouseleave', function() {
            $controls.removeClass('lurking active');
        });
        $container.on('mouseenter', function() {

            hasFocus = true;
            setTimeout(function() {
                // in case we left already
                if(hasFocus){
                    return;
                }
                $controls.hide().removeClass('lurking').fadeIn();
            }, 100);
        });
        $container.on('mouseleave', function() {
            $controls.hide();
            hasFocus = false;
        });
    }

    function init($container, config) {
        config.is = config.is || {};
        
        // just in case...
        if(!$container.length){
            return;
        }

        var $content  = $container.find('.sts-content'),
            $controls = $container.find('[class*=" sts-handle-"],[class^="sts-handle-"]').not('.sts-handle-move'),
            $launcher = $container.find('.sts-launch-button'),
            $closer   = $container.find('.sts-close'),
            $tool     = $container.find('.sts-container'),
            $wrapper  = $container.closest('.qti-infoControl'),
            tool      = $tool[0],
            handleSelector = (function() {
                var selectors = [];
                $controls.each(function(){
                    var cls = this.className.match(/sts-handle-rotate-[a-z]{1,2}/);
                    if(cls.length){
                        selectors.push('.' + cls[0]);
                    }
                });
                return selectors.join(',');
            }());

        // this needs to be a single DOM element
        // remove obsolete parent element
        //@todo order tools in toolbar
        var $toolbarContent = $('#' + config.toolbarId + ' > .sts-content'),
            $toolbarElements = $toolbarContent.find('.sts-toolcontainer');

        //   if(!$toolbarElements.length) {
        $toolbarContent.append($container);
        //   }
//        else {
//            $toolbarElements.each(function() {
//                var $existingContainer = $(this),
//                    existingPosition = $existingContainer.data('position');
//
//                if(existingPosition > config.position) {
//                    $existingContainer.before($container);
//                    return false;
//                }
//            });
//        }

        // forward the serial from the QTI wrapper element to the PIC container
        if ($wrapper.length) {
            $container.attr('data-pic-serial', $wrapper.data('serial'));
        }

        $container.removeAttr('style');

        $closer.on('click', function() {
            $tool.addClass('sts-hidden-container');
        });

        $launcher.off().on('click.sts', function() {
            // first run only
            if(!$tool.width()) {
                // having a defined width fixes chrome bug 'container scales instead of resizing'
                var cWidth = $container.width();
                $container.width(2000);
                $tool.removeClass('sts-hidden-container');
                $tool.width($content.width());
                $container.width(cWidth);
                $tool.addClass('sts-hidden-container');
            }
            $tool.toggleClass('sts-hidden-container');
        });

        // set up the controls for resize, rotate etc.
        setupControls($container, $controls);
        
        if (config.is.movable) {

            $content.on('mousedown', function () {
                this.style.cursor = 'move';
            }).on('mouseup', function () {
                this.style.cursor = 'default';
            });

            // init moving
            interact(tool)
                .draggable({max: Infinity})
                .on('dragstart', function (event) {
                    var $el = $(event.target);
                    event.interaction.x = parseInt($el.css('left'), 10) || 0;
                    event.interaction.y = parseInt($el.css('top'), 10) || 0;
                })
                .on('dragmove', function (event) {
                    event.interaction.x += event.dx;
                    event.interaction.y += event.dy;
                    event.target.style.left = event.interaction.x + 'px';
                    event.target.style.top = event.interaction.y + 'px';
                });
        }

        if (_.any(config.is.rotatable)) {
            rotator.init(tool, handleSelector);
        }
    }


    return {
        init: init
    };

});
