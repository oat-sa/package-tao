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
 *
 * @author dieter <dieter@taotesting.com>
 */
(function () {

    'use strict';

    var msgArea = document.querySelector('#requirement-check .requirement-msg-area'),
        tests = [
        ],
        i = tests.length;

    while(i--) {
        if(!Modernizr[tests[i]]) {
            msgArea.innerHTML = 'This browser is not supported by the TAO platform';
            document.documentElement.className = 'no-js';
            break;
        }
    }
}());

