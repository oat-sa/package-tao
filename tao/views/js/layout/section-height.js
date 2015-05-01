/**
 * @author Dieter Raber <dieter@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
], function($, _){
    'use strict';

        /**
         * Bar with the tree actions (providing room for two lines)
         *
         * @returns {number}
         */
        function getTreeActionIdealHeight() {
            // we need at least four actions to have a two-row ul
            var $treeActions = $('.tree-action-bar-box'),
                $treeActionUl = $treeActions.find('ul'),
                liNum = $treeActions.find('li:visible').length || 0,
                idealHeight;

            while(liNum < 5){
                $treeActionUl.append($('<li class="dummy"><a/></li>'));
                liNum++;
            }
            idealHeight = $treeActions.outerHeight() + parseInt($treeActions.css('margin-bottom'));
            $treeActionUl.find('li.dummy').remove();
            return idealHeight;
        }


        /**
         * Resize section heights
         * @private
         * @param {jQueryElement} $scope - the section scope
         */
        var setHeights = function setHeights($scope) {
            var $searchBar, 
                searchBarHeight,
                contentWrapperTop,
                footerTop,
                remainingHeight;
            var $contentPanel = $scope.is('.content-panel') ? $scope : $('.content-panel', $scope);
            var $tree         = $contentPanel.find('.taotree');

            if (!$contentPanel.length) {
                return;
            }
 
            $searchBar = $contentPanel.find('.search-action-bar');
            searchBarHeight = $searchBar.outerHeight() + parseInt($searchBar.css('margin-bottom')) + parseInt($searchBar.css('margin-top'));
            contentWrapperTop = $contentPanel.offset().top;
            footerTop = $('body > footer').offset().top;
            remainingHeight = footerTop - contentWrapperTop;

            $contentPanel.find('.content-container').css({ minHeight: remainingHeight });
            $tree.css({
                maxHeight: (footerTop - contentWrapperTop) - searchBarHeight - getTreeActionIdealHeight()
            });
        };

        /**
         * Helps you to manage the section heights
         * @exports layout/section-height
         */
        return {

            /**
             * Initialize behaviour of section height
             * @param {jQueryElement} $scope - the section scope
             */
            init : function($scope){

                $(window)
                    .off('resize.sectioneight')
                    .on('resize.sectionheight', _.debounce(function(){ 
                        setHeights($scope); 
                    }, 50));

                $('.version-warning')
                    .off('hiding.versionwarning')
                    .on('hiding.versionwarning', function(){

                    setHeights($scope);
                });
            },

            /**
             * Resize section heights
             * @param {jQueryElement} $scope - the section scope
             */
            setHeights: setHeights
        };
    });
