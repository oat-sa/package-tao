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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiTest/runner/helpers/map'
], function ($, _, __, mapHelper) {
    'use strict';

    /**
     * Tells is the current item has been answered or not
     * The item is considered answered when at least one response has been set to not empty {base : null}
     *
     * @returns {Boolean}
     */
    function isCurrentItemAnswered(runner) {
        var answered = false;
        _.forEach(runner.itemRunner && runner.itemRunner.getState(), function (state) {
            var response = state && state.response;

            if (_.isObject(response)) {
                // base or record defined: the interaction has a response, so the item is responded
                if (_.isObject(response.base) || _.isObject(response.record) || _.isArray(response.record)) {
                    answered = true;
                }
                else if (_.isObject(response.list)) {
                    _.forEach(response.list, function(entry) {
                        // list defined, and something is listed: the interaction has a response, so the item is responded
                        if (_.isArray(entry) && entry.length) {
                            answered = true;
                            return false;
                        }
                    });
                }

                if (answered) {
                    return false;
                }
            }
        });

        return answered;
    }

    /**
     * Completes an exit message
     * @param {String} message
     * @param {Object} runner
     * @returns {String} Returns the message text
     */
    function getExitMessage(message, scope, runner) {
        var map = runner.getTestMap();
        var context = runner.getTestContext();
        var stats = mapHelper.getScopeStats(map, context.itemPosition, scope);
        var unansweredCount = stats && (stats.total - stats.answered);
        var flaggedCount = stats && stats.flagged;
        var itemsCountMessage = '';
        var isItemCurrentlyAnswered;

        if (unansweredCount){
            isItemCurrentlyAnswered = isCurrentItemAnswered(runner);

            if (!isItemCurrentlyAnswered && context.itemAnswered) {
                unansweredCount++;
            }

            if (isItemCurrentlyAnswered && !context.itemAnswered) {
                unansweredCount--;
            }
        }


        if (flaggedCount && unansweredCount) {
            itemsCountMessage = __('You have %s unanswered question(s) and have %s item(s) marked for review.',
                unansweredCount.toString(),
                flaggedCount.toString()
            );
        } else {
            if (flaggedCount) {
                itemsCountMessage = __('You have %s item(s) marked for review.', flaggedCount.toString());
            }

            if (unansweredCount) {
                itemsCountMessage = __('You have %s unanswered question(s).', unansweredCount.toString());
            }
        }

        return (itemsCountMessage + ' ' + message).trim();
    }

    return {
        getExitMessage: getExitMessage
    };
});
