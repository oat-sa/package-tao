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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define(['lodash', 'ui/dialog'], function (_, dialog) {
    'use strict';

    /**
     * Displays an alert message
     * @param {String} message - The displayed message
     * @param {Function} action - An action called when the alert is closed
     * @returns {dialog} - Returns the dialog instance
     */
    return function dialogAlert(message, action) {
        var dlg = dialog({
            message: message,
            buttons: 'ok',
            autoRender: true,
            autoDestroy: true
        });

        if (_.isFunction(action)) {
            dlg.on('closed.modal', action);
        }
        return dlg;
    };
});
