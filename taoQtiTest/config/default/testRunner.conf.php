<?php
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
 */

/**
 * Default test runner config
 */
return array(


    //Show warning message if time remaining less than defined (in seconds)
    'timerWarning' => array(
        'assessmentItemRef' => null,
        'assessmentSection' => null,
        'testPart'          => null
    ),

    /**
     * Tells what type of progress bar to use? Can be:
     * - percentage : Classic progress bar displaying the percentage of answered items
     * - position : Progress bar displaying the position of the current item within the test session
     * @type string
     */
    'progress-indicator' => 'percentage',

    /**
     * When the `progress-indicator` option is set to `position`, define the scope of progress 
     * (i. e.: the number of items on which the ratio is computed). Can be:
     * - testSection : The progression within the current section
     * - testPart : The progression within the current part
     * - test : The progression within the current test
     * @type string
     */
    'progress-indicator-scope' => 'testSection',

    /**
     * Force the progress indicator to be always displayed
     * @type string
     */
    'progress-indicator-forced' => false,

    /**
     * Enables the test taker review screen
     * @type boolean
     */
    'test-taker-review' => false,

    /**
     * Position of the test taker review screen. Can be:
     * - left
     * - right
     * @type string
     */
    'test-taker-review-region' => 'left',

    /**
     * Forces a unique title for all test items.
     * @type string
     */
    'test-taker-review-force-title' => false,
    
    /**
     * A unique title for all test items, when the option `test-taker-review-force-title` is enabled.
     * This title will be processed through a sprintf() call, with the item sequence number as argument, 
     * so you can easily insert the sequence number inside the title. 
     * @type string
     */
    'test-taker-review-item-title' => 'Item %d',

    /**
     * Limits the test taker review screen to a particular scope. Can be:
     * - test : the whole test
     * - testPart : the current test part
     * - testSection : the current test section
     * @type string
     */
    'test-taker-review-scope' => 'test',

    /**
     * Prevents the test taker to access unseen items.
     * @type boolean
     */
    'test-taker-review-prevents-unseen' => true,
    
    /**
     * Allows the test taker to collapse the review screen: when collapsed the component is reduced to one tiny column.
     * @type boolean
     */
    'test-taker-review-can-collapse' => false,

    /**
     * Replace logout to exit button...
     * @type boolean
     */
    'exitButton' => false,

    /**
     * Allows the next section button...
     * @type boolean
     */
    'next-section' => false,

   /**
     * After resuming test session timers will be reset to the time when the last item has been submitted.
     * @type boolean
     */
    'reset-timer-after-resume' => false,
);
