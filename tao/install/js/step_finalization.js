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

        // Always nextable because no inputs in this template.
        install.setNextable(true);

        // Backward management.
        $('#install_seq li a').each(function() {
            $(this).bind('click', onBackward);
        });

        // What happens if you click on 'Proceed next step'.
        $('form').bind('submit', function(event) {
            $('#submitForm').attr('disabled', 'disabled')
                .removeClass('enabled')
                .addClass('disabled');

            if (install.isNextable()) {
                $('#deployment').css('visibility', 'visible');
                spinner.spin($('#deployment')[0]);

                // Install...
                var inputs = {
                    'module_url': install.getData('host_name'),
                    'module_namespace': install.getData('host_name') + '/' + install.getData('instance_name') + '.rdf',
                    'instance_name': install.getData('instance_name'),
                    'module_lang': install.getData('default_language'),
                    'module_mode': install.getData('deployment_mode'),
                    'import_local': install.getData('sample_data'),
                    'user_login': install.getData('superuser_login'),
                    'user_pass1': install.getData('superuser_password1'),
                    'user_lastname': install.getData('superuser_lastname'),
                    'user_firstname': install.getData('superuser_firstname'),
                    'user_email': install.getData('superuser_email'),
                    'db_host': install.getData('database_host'),
                    'db_user': install.getData('database_user'),
                    'db_pass': install.getData('database_password'),
                    'db_driver': install.getData('database_driver'),
                    'db_name': install.getData('database_name'),
                    'extensions': install.getData('extensions'),
                    'timezone': install.getData('timezone')
                };

                install.install(inputs, function(status, data) {
                    var success = false;
                    spinner.stop();

                    if (typeof(data) != 'undefined') {
                        // We received an HTTP 200 code...
                        if (data.value.status == 'valid') {
                            // This a success. In any other case, we fail gracefully below.
                            success = true;
                        }
                    }

                    if (success == true) {
                        $('#deployment').css('visibility', 'hidden');

                        // Redirection to the main TAO main (login) screen.
                        install.redirect('../../');
                    } else {
                        $('#deployment').css('visibility', 'hidden');
                        $('#submitForm').removeClass('disabled')
                            .addClass('enabled')
                            .attr('disabled', false);
                        displayTaoError(data.value.message);
                    }
                });
            }

            return false;
        });

        // Spin
        var spinner = new Spinner(getSpinnerOptions('small'));
    });
});
