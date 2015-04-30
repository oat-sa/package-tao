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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */
//load the AMD config
require(['config'], function() {
    require(['jquery', 'spin', 'help', 'jqueryui', 'steps'], function($, Spinner, TaoInstall) {

        var install = window.install;

        install.onNextable = function() {
            $('#submitForm').removeClass('disabled')
                .addClass('enabled')
                .attr('disabled', false);
            $('#submitForm').attr('value', 'Next');
        };

        install.onUnnextable = function() {
            $('#submitForm').removeClass('enabled')
                .addClass('disabled')
                .attr('disabled', true);
            $('#submitForm').attr('value', 'Next');
        };

        $('form').bind('submit', function() {
            if (install.isNextable()) {
                install.setTemplate('step_licenses');
            }

            return false;
        });

        // Backward management.
        $('#install_seq li a').each(function() {
            $(this).bind('click', onBackward);
        });

        // Initialize 'tao-input's.

        var firstValues = {};
        $('.tao-input').each(function() {
            $this = $(this);
            // Provide a data getter/setter for API handshake.
            install.getDataGetter(this);
            install.getDataSetter(this);

            // Get labelifed values from raw DOM for further comparison.
            if ($this.prop('tagName').toLowerCase() == 'input' && $this.attr('type') == 'text') {
                firstValues[this.id] = this.getData();
            }
        });

        // Backward management.
        $('#install_seq li a').each(function() {
            $(this).bind('click', onBackward);
        });


        // Register inputs.
        $('.tao-input').each(function() {

            if (typeof(firstValues[this.id]) != 'undefined') {
                this.firstValue = firstValues[this.id];
            }

            switch (this.id) {

                case 'superuser_firstname':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 1,
                        max: 30,
                        mandatory: false
                    });
                    validifyNotMandatory(this);
                    break;

                case 'superuser_lastname':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 1,
                        max: 30,
                        mandatory: false
                    });
                    validifyNotMandatory(this);
                    break;

                case 'superuser_email':
                    install.getValidator(this, {
                        dataType: 'email',
                        mandatory: false
                    });
                    validifyNotMandatory(this);
                    break;

                case 'superuser_login':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 1,
                        max: 30
                    });
                    validify(this);
                    break;

                case 'superuser_password1':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 4
                    });
                    validify(this);
                    break;

                case 'superuser_password2':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 4,
                        sameAs: 'superuser_password1'
                    });
                    validify(this);
                    break;

                default:
                    install.getValidator(this);
                    break;
            }

            install.register(this);

            // When data is changed, tell the Install API.
            $(".tao-input[type=text], .tao-input[type=password]").bind('keyup click change paste blur', function(event) {
                install.stateChange();
            });

        });

        // Populate form elements from API's data store.
        // (do not forget to restyle)
        $(install.populate()).each(function() {
            $(this).removeClass('helpTaoInputLabel');
        });

        initHelp();


        function initHelp() {
            install.addHelp('tpl_superuser_firstname', 'The first name of the Administrator.');
            install.addHelp('tpl_superuser_lastname', 'The last name of the Administrator.');
            install.addHelp('tpl_superuser_email', 'The email address of the Administrator.');
            install.addHelp('tpl_superuser_login', 'The account login of the Administrator.');
            install.addHelp('tpl_superuser_password', 'The Administrator password. Do not forget it.');
            install.addHelp('tpl_superuser_password2', 'Type the Administrator password again to make sure it\'s consistent.');
        }
    });
});
