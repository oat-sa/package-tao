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

        // Set up the list of available languages.
        var availableLanguages = install.getData('available_languages');
        if (availableLanguages != null) {
            var $defaultLanguageElement = $('#default_language').empty();

            for (var i in availableLanguages) {

                var selected = (i == 'en-US') ? 'selected="selected"' : '';
                $defaultLanguageElement.append('<option value="' + i + '" ' + selected + '>' + availableLanguages[i] + '</option>');
            }
        }

        // Set up the list of available timezones.
        var availableTimezones = install.getData('available_timezones');
        if (availableTimezones != null) {
            var $timezoneElement = $('#timezone').empty();

            for (var i in availableTimezones) {
                var selected = (availableTimezones[i] == 'UTC') ? 'selected="selected"' : '';
                $timezoneElement.append('<option value="' + availableTimezones[i] + '" ' + selected + '>' + availableTimezones[i] + '</option>');
            }
        }

        install.onNextable = function() {
            $('#submitForm').removeClass('disabled')
                .addClass('enabled')
                .attr('disabled', false);
            $('#submitForm').attr('value', 'Next');
        }

        install.onUnnextable = function() {
            $('#submitForm').removeClass('enabled')
                .addClass('disabled')
                .attr('disabled', true);
            $('#submitForm').attr('value', 'Next');
        }

        $('form').bind('submit', function() {
            if (install.isNextable()) {
                install.setTemplate('step_database_setup');
            }

            return false;
        });

        var firstValues = {};
        $('.tao-input').each(function() {
            $this = $(this);
            // Provide a data getter/setter for API handshake.
            install.getDataGetter(this);
            install.getDataSetter(this);

            // Get labelifed values from raw DOM.
            if ($this.prop('tagName').toLowerCase() == 'input') {
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

                case 'host_name':
                    install.getValidator(this, {
                        dataType: 'host'
                    });
                    validify(this);
                    break;

                case 'instance_name':
                    install.getValidator(this, {
                        dataType: 'regexp',
                        pattern: "^[a-zA-Z0-9_\-]{3,63}$"
                    });
                    validify(this);
                    break;

                case 'default_language':
                    this.isValid = function() {
                        return true;
                    };
                    break;

                default:
                    install.getValidator(this);
                    break;
            }

            install.register(this);

            // When data is changed, tell the Install API.
            $(this).bind('keyup click change paste blur', function(event) {
                install.stateChange();
            });
        });

        // Populate form elements from API's data store.
        // (do not forget to restyle)
        $(install.populate()).each(function() {
            $(this).removeClass('helpTaoInputLabel');
        });

        // If after population, there is no value for host_name,
        // provide a default one if possible.
        if (install.getData('root_url') != null && install.getData('host_name') == null) {
            $('#host_name').removeClass('helpTaoInputLabel')[0].setData(install.getData('root_url'));
        }

        initHelp();

        install.stateChange();

        function initHelp() {
            install.addHelp('hlp_host_name', "This field must contain the entire URL (Uniform Resource Locator) that locates your TAO platform. The default value should work in any case.");
            install.addHelp('hlp_instance_name', "The instance name will be allocated to this installation to differentiate it from others across your network. The range of accepted characters for this field are alphanumeric characters, underscore (_) and dash (-).");
            install.addHelp('hlp_default_language', "The default language used by TAO to display texts in the graphical user interface.");
            install.addHelp('hlp_timezone', "The desired time zone to be used by your web server to deal with time constraints.");
            install.addHelp('hlp_deployment_mode', "The <em>production</em> deployment mode provides you with a secure installation dedicated to production. On the other hand, the <em>development</em> mode is dedicated to developers where various debug modes are enabled.");
        }

    });
});
