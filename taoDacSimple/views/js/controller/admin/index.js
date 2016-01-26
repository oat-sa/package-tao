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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'tpl!taoDacSimple/controller/admin/line',
    'helpers',
    'ui/feedback',
    'ui/autocomplete',
    'tooltipster',
    'jqueryui'
], function ($, _, __, lineTpl, helpers, feedback, autocomplete) {
    'use strict';

    /**
     * The amount of displayed lines that fires the tabs mode
     * @type {Number}
     */
    var linesThreshold = 10;

    /**
     * The warning message shown when all managers have been removed
     * @type {String}
     */
    var errorMsgManagePermission = __('You must have one role or user that have the manage permission on this element.');

    /**
     * Config object needed by the tooltip used to display warning if all managers have been removed
     * @type {Object}
     */
    var tooltipConfigManagePermission = {
        content : __(errorMsgManagePermission),
        theme : 'tao-warning-tooltip',
        trigger: 'hover'
    };

    /**
     * Checks the managers, we need at least one activated manager.
     * @param {jQuery|Element|String} container
     * @returns {Boolean} Returns `true` if there is at least one manager in the list
     * @private
     */
    var _checkManagers = function (container) {
        var $managers = $(container).find('.privilege-GRANT:checked');
        var checkOk = true;

        if (!$managers.length) {
            checkOk = false;
        }
        return checkOk;
    };

    /**
     * Avoids to remove all managers
     * @param {jQuery|Element|String} container
     * @private
     */
    var _preventManagerRemoval = function(container){
        var $form = $(container).closest('form');
        var $submitter = $(':submit', $form);

        $submitter.tooltipster(tooltipConfigManagePermission);
        if (!_checkManagers($form)) {
            $submitter.addClass('disabled').tooltipster('enable');
            feedback().warning(errorMsgManagePermission);
        } else {
            $submitter.removeClass('disabled').tooltipster('disable');
        }
    };

    /**
     * Allow to enable / disable the access checkbox based on the state of the grant privilege
     * @param {jQuery|Element|String} container
     * @private
     */
    var _disableAccessOnGrant = function (container) {
        var $container = $(container);

        var $managersChecked = $container.find('.privilege-GRANT:checked').closest('tr');
        var $cantChangeWrite = $managersChecked.find('.privilege-WRITE');
        var $cantChangeRead = $managersChecked.find('.privilege-READ');

        var $managers = $container.find('.privilege-GRANT').not(':checked').closest('tr');
        var $canChangeWrite = $managers.find('.privilege-WRITE');
        var $canChangeRead = $managers.find('.privilege-READ');

        $canChangeWrite.removeClass('disabled');
        $canChangeRead.removeClass('disabled');

        $cantChangeWrite.addClass('disabled').attr('checked', true);
        $cantChangeRead.addClass('disabled').attr('checked', true);

        _preventManagerRemoval($container);
        _disableAccessOnWrite($container);
    };

    /**
     * Allow to enable / disable the access checkbox based on the state of the write privilege
     * @param {jQuery|Element|String} container
     * @private
     */
    var _disableAccessOnWrite = function (container) {
        var $container = $(container);

        var $writersChecked = $container.find('.privilege-WRITE:checked').closest('tr');
        var $cantChangeRead = $writersChecked.find('.privilege-READ');

        var $writers = $container.find('.privilege-WRITE').not(':checked').closest('tr');
        var $canChangeRead = $writers.find('.privilege-READ');

        $canChangeRead.removeClass('disabled');

        $cantChangeRead.addClass('disabled').attr('checked', true);
    };

    /**
     * Delete a permission row for a user/role
     * @param  {DOM Element} element DOM element that triggered the function
     * @private
     */
    var _deletePermission = function (element) {
        // 1. Get the user / role
        var $this = $(element);
        var $container = $this.closest('table');
        var type = $this.data('acl-type');
        var user = $this.data('acl-user');
        var label = $this.data('acl-label');

        // 2. Remove it from the list
        if (!_.isEmpty(type) && !_.isEmpty(user) && !_.isEmpty(label)) {
            $this.closest('tr').remove();
        }

        _preventManagerRemoval($container);
        _manageTabsDisplay();
    };

    /**
     * Checks if a permission has already been added to the list.
     * Highlight the list if the permission is already in the list.
     * @param {jQuery|Element|String} container
     * @param {String} type role/user regarding what it will be added.
     * @param {String} id The identifier of the resource.
     * @returns {boolean} Returns true if the permission is already in the list
     * @private
     */
    var _checkPermission = function (container, type, id) {
        var $btn = $(container).find('button[data-acl-user="' + id + '"]'),
            $line = $btn.closest('tr');

        if ($line.length) {
            $line.effect('highlight', {}, 1500);
            return true;
        }

        return false;
    };

    /**
     * Add a new lines into the permissions table regarding what is selected into the add-* select
     * @param {jQuery|Element|String} container
     * @param {String} type role/user regarding what it will be added.
     * @param {String} id The identifier of the resource.
     * @param {String} label The label of the resource.
     * @private
     */
    var _addPermission = function (container, type, id, label) {
        var $container = $(container),
            $body = $container.find('tbody').first();

        // only add the permission if it's not already present in the list
        if (!_checkPermission($container, type, id)) {
            $body.append(lineTpl({
                type: type,
                user: id,
                label: label
            }));
            _disableAccessOnGrant($container);
            _manageTabsDisplay();
        }
    };

    /**
     * Ensures that if you give the manage (GRANT) permission, access (WRITE and READ) permissions are given too
     * Listens all clicks on delete buttons to call the _deletePermission function
     * @param {jQuery|Element|String} container The container on which apply the listeners
     * @private
     */
    var _installListeners = function(container) {
        var $container = $(container);
        $container.on('click', '.privilege-GRANT:not(.disabled) ', function () {
            _disableAccessOnGrant($container);
        }).on('click', '.privilege-WRITE:not(.disabled) ', function () {
            _disableAccessOnWrite($container);
        }).on('click', '.delete_permission:not(.disabled)', function (event) {
            event.preventDefault();
            _deletePermission(this);
        });
    };

    /**
     * Manages the display of tabs.
     * If the total amount of lines per tables is too big, display the tabs. Otherwise, hide them.
     * @private
     */
    var _manageTabsDisplay = function() {
        var $tabs = $('.permission-tabs');
        var needsTabs = $tabs.find('.privilege-GRANT').length > linesThreshold;
        var $focused, index;

        if (needsTabs) {
            $tabs.find('ul').show();
            if (!$tabs.hasClass('ui-tabs')) {
                // get the current focused panel
                $focused = $tabs.find(':focus').closest('.permission-tabs-panel');
                index = Math.max(0, $focused.index() - 1);

                // install the tabs, but keep the current panel focused
                $tabs.tabs({
                    // use two options to be compatible with both older and current version of jQueryUI
                    selected: index,
                    active: index
                });
            }
            $('.msg-edit-area label span').hide();
        } else {
            if ($tabs.hasClass('ui-tabs')) {
                $tabs.find('.ui-tabs-hide').removeClass('ui-tabs-hide');
                $tabs.tabs('destroy');
            }
            $tabs.find('ul').hide();
            $('.msg-edit-area label span').show();
        }
    };

    /**
     * Installs a search purpose autocompleter onto an element.
     * @param {jQuery|Element|String} element The element on which install the autocompleter
     * @param {Object} options A list of options to set
     * @returns {Autocompleter} Returns the instance of the autocompleter component
     */
    var _searchFactory = function (element, options) {
        if (_.isFunction(options)) {
            options = {
                onSelectItem: options
            };
        }

        options = _.assign({
            isProvider: true,
            preventSubmit: true
        }, options || {});

        return autocomplete(element, options);
    };

    var mainCtrl = {
        'start': function () {

            var $container = $('.permission-container');
            var $form = $('form', $container);
            var $submitter = $(':submit', $form);

            _disableAccessOnGrant('#permissions-table-users');
            _disableAccessOnGrant('#permissions-table-roles');

            // install autocomplete for user add
            _searchFactory('#add-user', function (event, value, label) {
                $('#add-user').focus();
                _addPermission('#permissions-table-users', 'user', value, label);
            });

            // install autocomplete for role add
            _searchFactory('#add-role', function (event, value, label) {
                $('#add-role').focus();
                _addPermission('#permissions-table-roles', 'role', value, label);
            });

            // ensure that if you give the manage (GRANT) permission, access (WRITE and READ) permissions are given too
            _installListeners('#permissions-table-users');
            _installListeners('#permissions-table-roles');

            _manageTabsDisplay();

            $form.on('submit', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            });
            $submitter.on('click', function (e) {
                e.preventDefault();

                if ($submitter.hasClass('disabled')) {
                    return;
                }

                if (!_checkManagers('form')) {
                    feedback().error(errorMsgManagePermission);
                   return;
                }

                $submitter.addClass('disabled');

                $.post($form.attr('action'), $form.serialize())
                    .done(function (res) {
                        if (res && res.success) {
                            feedback().success(__("Permissions saved"));
                        } else {
                            feedback().error(__("Something went wrong..."));
                        }
                    })
                    .complete(function () {
                        $submitter.removeClass('disabled');
                    });
            });
        }
    };

    return mainCtrl;
});
