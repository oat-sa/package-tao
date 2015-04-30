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
    'i18n',
    'module',
    'ui/feedback',
    'layout/version-warning'
], function ($, __, module, feedback,  versionWarning) {
    'use strict';

    var conf, type, context = $('.entry-pages-container'), $fields = $();

    versionWarning.init();

    $('input[type="text"]').eq(0).focus();

    // empty $fields sent
    if(context.find('.form-error').length){
        conf = {
            message: {
                error: __('All fields are required')
            }
        };
        context.find(':input').each(function() {
            if(!this.value) {
                $fields = $fields.add($(this));
            }
        });
    }
    // if the module config contains a message object
    else {
        conf = module.config();
    }

    // any error/info creates feedback
    if (conf.message) {
        $fields = context.find(':input');
        for (type in conf.message) {
            if (!conf.message[type]) {
                continue;
            }
            feedback()[type](conf.message[type]);
            $fields.addClass(type);
        }
    }

});
