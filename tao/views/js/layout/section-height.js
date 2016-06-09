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
 * @author Dieter Raber <dieter@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash'
], function ($, _) {

    'use strict';


    var $versionWarning = $('.version-warning:visible'),
        $window = $(window),
        $footer = $('body > footer');

    /**
     * Bar with the tree actions (providing room for at least two rows of buttons)
     *
     * @returns {number}
     */
    function getTreeActionIdealHeight() {
        var $visibleActionBarBox = $('.tree-action-bar-box'),
            $visibleActionBar    = $visibleActionBarBox.find('.tree-action-bar'),
            $mainButtons         = $visibleActionBar.find('li'),
            $visibleButtons      = $mainButtons.filter(':visible'),
            // at least two rows
            $requiredRows        = Math.max(Math.ceil($mainButtons.length/4), 2),
            idealHeight;

        if(!$visibleButtons.length) {
            $visibleButtons = $('<li class="dummy"><a/></li>');
            $visibleActionBar.append($visibleButtons);
        }

        idealHeight = ($visibleButtons.outerHeight(true) * $requiredRows) +
            parseInt($visibleActionBarBox.css('margin-bottom')) +
            parseInt($visibleActionBarBox.css('margin-top'));
        $visibleButtons.filter('.dummy').remove();

        return idealHeight;
    }
    

    /**
     * Compute the height of the navi- and content container
     *
     * @param $scope jQueryElement
     * @returns {number}
     */
    function getContainerHeight($scope) {
        var winHeight = $window.innerHeight(),
            footerHeight = $footer.outerHeight(),
            headerHeight = $('header.dark-bar').outerHeight() + ($versionWarning.length ? $versionWarning.outerHeight() : 0),
            actionBarHeight = $scope.find('.content-container .action-bar').outerHeight(),
            $tabs = $('.section-container > .tab-container:visible'),
            tabHeight = $tabs.length ? $tabs.outerHeight() : 0;

        return winHeight - headerHeight - footerHeight - actionBarHeight - tabHeight;
    }


    /**
     * Resize section heights
     * @private
     * @param {jQueryElement} $scope - the section scope
     */
    function setHeights($scope) {
        var containerHeight = getContainerHeight($scope),
            $contentBlock = $scope.find('.content-block'),
            $tree = $scope.find('.taotree');

        if (!$tree.length) {
            return;
        }

        $contentBlock.css( { height: containerHeight, maxHeight: containerHeight });
        $tree.css({
            maxHeight: containerHeight - getTreeActionIdealHeight()
        });
    }

    /**
     * Helps you to manage the section heights
     * @exports layout/section-height
     */
    return {

        /**
         * Initialize behaviour of section height
         * @param {jQueryElement} $scope - the section scope
         */
        init: function ($scope) {


            $window
                .off('resize.sectionheight')
                .on('resize.sectionheight', _.debounce(function () {
                    setHeights($scope);
                }, 50));

            $versionWarning
                .off('hiding.versionwarning')
                .on('hiding.versionwarning', function () {
                    $versionWarning = $('.version-warning:visible');
                    setHeights($scope);
                });

            // Resizing the section can cause scroll bars to appear
            // and hence the viewport might change. This in return could
            // cause the <nav>s to nudge
            $(window).trigger('resize.navheight');
        },

        /**
         * Resize section heights
         * @param {jQueryElement} $scope - the section scope
         */
        setHeights: setHeights
    };
});
