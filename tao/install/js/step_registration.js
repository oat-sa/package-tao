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

        var registrationRootUrl = 'http://forge.taotesting.com/registration/';
        var isRegFormLoaded = false;
        var isUpdateFormLoaded = false;
        var taoVersion = '2.6';
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

        // Nextable, unless default choice is already registered
        //install.setNextable(true);

        $('form').bind('submit', function() {
            if (install.isNextable()) {
                install.setTemplate('step_requirements');
            }

            return false;
        });

        $('form').unbind('submit').bind('submit', function() {
            if (install.isNextable()) {
                install.setTemplate('step_finalization');
            }

            return false;
        });

        $('input#radio-noreg').click(function() {

            $('ul#support_fields').slideUp();
            $('ul#registration_fields').slideUp();
            $('p#formComment').fadeOut(150);

            $('input#submitForm').removeClass('disabled')
                .addClass('enabled')
                .attr('disabled', false);

            $('form').unbind('submit').bind('submit', function() {
                if (install.isNextable()) {
                    install.setTemplate('step_finalization');
                }

                return false;
            });

            $('#flag_notreg').val('');
            $('#support_login').removeClass('tao-input');
            $('#support_password').removeClass('tao-input');
            install.stateChange();
        });

        $('input#radio-askreg').click(function() {
            $('ul#support_fields').slideUp();
            $('p#formComment').fadeIn(150);
            $('ul#registration_fields').slideDown();

            if (!isRegFormLoaded) {

                $('ul#registration_fields').load(registrationRootUrl + 'taoForgeRegister.php?tao_version=' + encodeURI(taoVersion), function(response, status, xhr) {
                    if (status === "error") {

                        //console.log( msg + xhr.status + " " + xhr.statusText );
                        var msg = "There was an error when trying to call the registration page. The service may be currently unavailable.";
                        displayTaoError(msg);

                        isRegFormLoaded = false;
                        setFallback();
                    } else {
                        isRegFormLoaded = true;
                    }
                });
            }

            $('#flag_notreg').val('');
            $('#support_login').removeClass('tao-input');
            $('#support_password').removeClass('tao-input');
            install.stateChange();
        });

        $('input#radio-alreadyreg').click(function() {
            $('ul#support_fields').slideDown();
            $('ul#registration_fields').slideUp();
            $('p#formComment').fadeIn(300);

            $('input#submitForm').removeClass('disabled')
                .addClass('enabled')
                .attr('disabled', false);

            if (!isUpdateFormLoaded) {

                $('ul#support_fields').load(registrationRootUrl + 'taoForgeRegistrationUpdate.php?tao_version=' + taoVersion, function(response, status, xhr) {
                    if (status === "error") {

                        //console.log( msg + xhr.status + " " + xhr.statusText );
                        var msg = "There was an error when trying to access the registration update page. The service may be currently unavailable.";
                        displayTaoError(msg);

                        isRegFormLoaded = false;
                        setFallback();
                    } else {
                        isUpdateFormLoaded = true;
                    }
                });
            }

            $('#flag_notreg').val('');
            $('#support_login').addClass('tao-input');
            $('#support_password').addClass('tao-input');
            install.stateChange();
        });

        // Initialize 'tao-input's.

        var firstValues = {};
        $('.tao-input').each(function() {
            $this = $(this);
            // Provide a data getter/setter for API handshake.
            install.getDataGetter(this);
            install.getDataSetter(this);

            // Get labelifed values from raw DOM.
            if ($this.prop('tagName').toLowerCase() === 'input' && $this.attr('type') === 'text') {
                firstValues[this.id] = this.getData();
            }
        });

        // Backward management
        $('#install_seq li a').each(function() {
            $(this).bind('click', onBackward);
        });

        // Register inputs
        $('.tao-input').each(function() {
            if (typeof(firstValues[this.id]) !== 'undefined') {
                this.firstValue = firstValues[this.id];
            }

            switch (this.id) {

                case 'support_login':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 3,
                        max: 30
                    });
                    validify(this);
                    break;

                case 'support_password':
                    install.getValidator(this, {
                        dataType: 'string',
                        min: 8,
                        max: 30
                    });
                    validify(this);
                    break;

                case 'flag_notreg':
                    install.getValidator(this, {
                        dataType: 'regexp',
                        'pattern': '[0-9]+'
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

            $(".tao-input[type=radio]").bind("change", function(event) {
                install.stateChange();
            });
        });

        // Populate form elements from API's data store.
        // (do not forget to restyle)
        $(install.populate()).each(function() {
            $(this).removeClass('helpTaoInputLabel');
        });

        // Initial state: encourage to register
        //$('input#radio-askreg').click();
        $('#flag_notreg').val('1');
        /*$('input#radio-alreadyreg').click();
    $('#flag_notreg').val('');*/
        $('support_login').removeClass('tao-input');
        $('support_password').removeClass('tao-input');
        install.stateChange();

        //$('input#submitForm').focus();

        // initial state: two first radios are disabled until TAO forge connection is checked
        $('input#radio-askreg').removeClass('enabled')
            .addClass('disabled')
            .attr('disabled', true);
        $('input#radio-alreadyreg').removeClass('enabled')
            .addClass('disabled')
            .attr('disabled', true);
        $('input#radio-noreg').click();
        $('input#redoForm').hide();

        // check connection to TAO Forge
        // set a spinner up
        $forgeAccountMsg = $('#forge-account');
        $forgeAccountMsg.css('visibility', 'visible');
        var spinner = new Spinner(getSpinnerOptions('small')).spin($forgeAccountMsg[0]);

        setTimeout(function() {

            var host = registrationRootUrl;

            var connection_status = taoForgeConnectionCheck(host);
            //console.log( 'connection_status: '+connection_status );

            $forgeAccountMsg.css('visibility', 'hidden');
            spinner.stop();

            var check = {
                host: host
                /*,
                            user: user,
                            password: password*/
            };

            /*install.checkTAOForgeConnection(check, function(status, data){
                    $forgeAccountMsg.css('visibility', 'hidden');
                    spinner.stop();

                    if (data.value.status == 'valid'){
                        
                        $('input#radio-askreg').removeClass('disabled')
                            .addClass('enabled')
                            .attr('disabled', false);
                        $('input#radio-alreadyreg').removeClass('disabled')
                            .addClass('enabled')
                            .attr('disabled', false);
                    }
                    else if (data.value.status == 'invalid-noconnection'){
                            // No connection could be established.
                            var msg  = "Unable to connect to the TAO Forge ("+host+"). Please check external network availability.";
                            displayTaoError(msg);
                    }
                    else if (data.value.status == 'invalid-not-existing'){
                            // Connection could be established but credentials check failed.
                            var msg = "TAO Forge credentials check failed. Please re-check your login and password!";
                            displayTaoError(msg);
                    }
            });*/
        }, 1000); // fake additional delay for user (1000ms).

        initHelp();

        // disable next first       
        $('#submitForm').removeClass('enabled')
            .addClass('disabled')
            .attr('disabled', true);

        function taoForgeConnectionCheck(url) {

            $.ajax({
                url: url,
                complete: function(jqxhr, txt_status) {
                    //console.log ("Complete: [ " + txt_status + " ] -- Status code: " + jqxhr.status);

                    if (txt_status !== 'success') {

                        var msg = "Unable to connect to the TAO Forge registration service. Currently either this installer has no internet access or the service is unavailable.\n\nThus the two first options for registration are disabled. If you wish to register your installation at this step, please check external network availability then click on the Re-check button.";
                        displayTaoError(msg, 'Warning');

                        setFallback();
                    } else { // success

                        $('input#radio-askreg').removeClass('disabled')
                            .addClass('enabled')
                            .attr('disabled', false);
                        $('input#radio-alreadyreg').removeClass('disabled')
                            .addClass('enabled')
                            .attr('disabled', false);

                        // hide refresh button
                        $('redoForm').removeClass('enabled')
                            .addClass('disabled')
                            .attr('enabled', false);

                        // remove the spinner-banner (we lack some space!)
                        $('div#forge-account').remove();

                        // select first choice (new registration)
                        $('input#radio-askreg').click();

                        $('input#redoForm').hide();
                    }

                    return txt_status;
                }
            });
        }

        function setFallback() {

            $('input#radio-askreg').removeClass('enabled')
                .addClass('disabled')
                .attr('disabled', true);
            $("label[for='radio-askreg']").css('color', '#aaa');
            $('input#radio-alreadyreg').removeClass('enabled')
                .addClass('disabled')
                .attr('disabled', true);
            $("label[for='checkbox-forge-credentials']").css('color', '#aaa');

            // display refresh button
            $('redoForm').removeClass('disabled')
                .addClass('enabled')
                .attr('disabled', false);

            // select third choice
            $('input#radio-noreg').click();

            $('input#redoForm').show();

            //$('input#submitForm').focus();

            // hide refresh button
            $('redoForm').removeClass('disabled')
                .addClass('enabled')
                .attr('disabled', false);
        }

        function initHelp() {

            install.addHelp('tpl_support_login', 'Your current login on TAO forge.');
            install.addHelp('tpl_support_password', 'Your current password on TAO forge. Used jointly with your login to grant you access to this TAO forge account.');

            install.addHelp('tpl_registration_firstname', 'Your first name displayed on TAO forge.');
            install.addHelp('tpl_registration_lastname', 'Your last name displayed on TAO forge.');
            install.addHelp('tpl_registration_organization', 'Your company name displayed on the TAO forge.');
            install.addHelp('tpl_registration_email', 'Your email address displayed on TAO forge. You may choose to keep it private later in settings.');
            install.addHelp('tpl_registration_login', 'Your TAO forge login.');
            install.addHelp('tpl_registration_password', 'Your TAO forge password.');
            install.addHelp('tpl_registration_contactme', 'By checking this box, you give us permission to contact you at the email address provided above.');
        }
    });
});
