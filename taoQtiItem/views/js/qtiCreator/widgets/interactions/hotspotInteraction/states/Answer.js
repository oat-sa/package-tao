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
    'taoQtiItem/qtiCreator/widgets/interactions/states/Answer',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState'
], function(stateFactory, Answer, answerStateHelper){

    'use strict';

    /**
     * Just forward to the correct/map states
     */
    var initAnswerState = function initAnswerState(){
        answerStateHelper.forward(this.widget);
    };
    
    
    var exitAnswerState = function exitAnswerState(){
        //needed an exit callback even empty
    };
    
    
    /**
     * The answer state for the hotspot interaction
     * @extends taoQtiItem/qtiCreator/widgets/interactions/states/Answer
     * @exports taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Answer
     */
    return stateFactory.extend(Answer, initAnswerState, exitAnswerState);
});
