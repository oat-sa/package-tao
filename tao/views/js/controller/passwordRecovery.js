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

/**
 * Recovery password page controller
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
define([
    'jquery',
    'i18n',
    'module',
    'ui/feedback',
    'layout/version-warning'
], function ($, __, module, feedback,  versionWarning) {
    'use strict';
    var conf = module.config(),
        feedbackType;

    versionWarning.init();
    if (conf.message) {
        for (feedbackType in conf.message) {
            if (conf.message[feedbackType]) {
                feedback()[feedbackType](conf.message[feedbackType]);
            }
        }
    }
});
