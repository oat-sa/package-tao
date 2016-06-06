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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['taoItems/runner/api/itemRunner', 'taoQtiItem/runner/provider/qti'], function(itemRunner, qtiRuntimeProvider){
    'use strict';

    //register the QTI Provider
    itemRunner.register('qti', qtiRuntimeProvider);

    /**
     * Expose the itemRunner with the QTI provider registered
     * @exports taoQtiItem/runner/qtiItemRunner
     */
    return itemRunner;
});
