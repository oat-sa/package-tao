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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * The controller dedicated to the login page.
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'module',
    'ui/feedback',
    'layout/version-warning'
], function ($, _, __, module, feedback, versionWarning) {
    'use strict';

    var conf = module.config();
    var messages = conf.message || {};
    var $context = $('.entry-point-container');
    var $loginForm = $context.find('#loginForm');
    var $fakeForm = $context.find('.fakeForm');

    /**
     * Submits the form after a copy of all the inputs the user has made in the fake form
     */
    function submitForm() {
        // if the fake form exists, copy all fields values into the real form
        $fakeForm.find(':input').each(function () {
            var $field = $(this);
            $loginForm.find('input[name="' + $field.attr('name') + '"]').val($field.val());
        });

        // just submit the real form as if the user did it
        $loginForm.submit();
    }

    /**
     * Displays the error/info messages
     * @param {Object} messages
     */
    function displayMessages(messages) {
        var $fields = $context.find(':input');
        var renderer = feedback();
        _.forEach(messages, function (message, type) {
            if (message) {
                if (_.isFunction(renderer[type])) {
                    renderer[type](message);
                }
                $fields.addClass(type);
            }
        });
    }

    versionWarning.init();

    // empty $fields sent
    if (!messages.error && $context.find('.form-error').length) {
        messages.error = __('All fields are required');
    }

    // any error/info creates feedback
    displayMessages(messages);

    // submit the form when the user hit the submit button inside the fake form
    $fakeForm.find('input[type="submit"], button[type="submit"]').off('click').on('click', function (e) {
        e.preventDefault();
        submitForm();
    });

    // submit the form when the user hit the ENTER key inside the fake form
    $fakeForm.on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            submitForm();
        }
    });
});
