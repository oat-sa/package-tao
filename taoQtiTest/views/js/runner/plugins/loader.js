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
define([
    'taoTests/runner/pluginLoader',
    'taoQtiTest/runner/plugins/content/rubricBlock/rubricBlock',
    'taoQtiTest/runner/plugins/content/overlay/overlay',
    'taoQtiTest/runner/plugins/content/dialog/dialog',
    'taoQtiTest/runner/plugins/content/feedback/feedback',
    'taoQtiTest/runner/plugins/controls/duration/duration',
    'taoQtiTest/runner/plugins/controls/title/title',
    'taoQtiTest/runner/plugins/controls/timer/timer',
    'taoQtiTest/runner/plugins/controls/progressbar/progressbar',
    'taoQtiTest/runner/plugins/controls/review/review',
    'taoQtiTest/runner/plugins/navigation/next',
    'taoQtiTest/runner/plugins/navigation/previous',
    'taoQtiTest/runner/plugins/navigation/nextSection',
    'taoQtiTest/runner/plugins/navigation/skip',
    'taoQtiTest/runner/plugins/tools/comment/comment'
], function(pluginLoader, rubricBlock, overlay, dialog, feedback, duration, title, timer, progressbar, review, next, previous, nextSection, skip, comment) {
    'use strict';

    /**
     * Instantiate the plugin loader with all the required plugins configured
     */
    return pluginLoader({
        content    : [rubricBlock, overlay, dialog, feedback],
        controls   : [title, timer, progressbar, duration],
        navigation : [review, previous, next, nextSection, skip],
        tools      : [comment]
    });
});
