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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */


/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory', 
    'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep'
], function(stateFactory, SleepState){

    'use strict';
   
    var initSleepState = function initSleepState(){
        var widget      = this.widget;
        var interaction = widget.element;
        widget.on('metaChange', function(data){
            if(data.key === 'responsive'){
                if(data.value === true){
                    interaction.addClass('responsive');
                } else {
                    interaction.removeClass('responsive');
                }
                widget.rebuild();
            }
        });
    };

    var exitSleepState = function exitSleepState(){
        $('.image-editor.solid, .block-listing.source', this.widget.$container).css('min-width', 0);
    };
 
    return stateFactory.extend(SleepState, initSleepState, exitSleepState); 
});
