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

        $('.tao-input').each(function() {
            // Provide a data getter/setter for API handshake.
            install.getDataGetter(this);
            install.getDataSetter(this);

            install.getValidator(this, {
                dataType: 'regexp',
                'pattern': '[0-9]+'
            });
            install.register(this);
        });

        // Backward management.
        $('#install_seq li a').each(function() {
            $(this).bind('click', onBackward);
        });

        $('form').bind('submit', function() {
            if (install.isNextable()) {
                install.setTemplate('step_finalization');
            }

            return false;
        });

        $(function() {
            $('#dialog-license1-confirm').dialog({
                autoOpen: false,
                resizable: false,
                draggable: false,
                height: 450,
                width: 700,
                modal: true,
                buttons: {
                    "button-accept": {
                        text: "I have read and agree to the Terms and Conditions",
                        id: "license1-button-accept",
                        click: function() {
                            $(this).dialog("close");
                        }
                    },
                    "button-refuse": {
                        text: "Cancel",
                        id: "license1-button-refuse",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }
                }
            });
        });

        $(function() {
            $('#dialog-license2-confirm').dialog({
                autoOpen: false,
                resizable: false,
                draggable: false,
                height: 450,
                width: 700,
                modal: true,
                buttons: {
                    "button-accept": {
                        text: "I have read and agree to the Terms and Conditions",
                        id: "license2-button-accept",
                        click: function() {
                            $(this).dialog("close");
                        }
                    },
                    "button-refuse": {
                        text: "Cancel",
                        id: "license2-button-refuse",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }
                }
            });
        });

        $('#readLicense1').click(
            function() {
                $('#dialog-license1-confirm').dialog('open');
            }
        ).focus();

        $('#readLicense2').click(
            function() {
                $('#dialog-license2-confirm').dialog('open');
            }
        );

        $('#license1-button-accept').click(
            function() {
                $('#approval-status-1')
                    .text("You have reviewed and accepted the terms of this license.")
                    .append('&nbsp;<img src="images/valide.png" />');
                $('#readLicense2').focus();

                $('#gplRead').val('1');
                install.stateChange();
            }
        );
        $('#license1-button-refuse').click(
            function() {
                $('#approval-status-1')
                    .text("You have not accepted the terms of the license.")
                    .append('&nbsp;<img src="images/failed.png" />');

                $('#gplRead').val('');
                install.stateChange();
            }
        );

        $('#license2-button-accept').click(
            function() {
                $('#approval-status-2')
                    .text("You have reviewed and accepted the terms of this license.")
                    .append('&nbsp;<img src="images/valide.png" />');

                $('#trademarkRead').val('1');
                install.stateChange();

                $('input#submitForm').focus();
            }
        );

        $('#license2-button-refuse').click(
            function() {
                $('#approval-status-2')
                    .text("You have not accepted the terms of the license.")
                    .append('&nbsp;<img src="images/failed.png" />');

                $('#trademarkRead').val('');
                install.stateChange();
            }
        );

        // loading of all licenses and license headers
        $('textarea#readLicense1Header').load('licenses/gnu_gplv2_header.txt');
        $('#dialog-license1-confirm').load('licenses/gnu_gplv2_license.html');
        $('textarea#readLicense2Header').load('licenses/tao_trademark_header.txt');
        $('#dialog-license2-confirm').load('licenses/tao_trademark.html');

        $('#abortForm').bind('click', function(event) {
            install.setTemplate('step_requirements');
        });

        // license approval status   

        // force accepted license
        //$('#gplRead').val('1');

        if ($('#gplRead').val() === '1') {

            $('#approval-status-1')
                .text("You have reviewed and accepted the terms of this license.")
                .append('<img src="images/valide.png" />');

        } else {
            $('#approval-status-1').text('Please review and approve the terms of this license.');
        }

        // force accepted license
        //$('#trademarkRead').val('1');

        if ($('#trademarkRead').val() === '1') {

            $('#approval-status-2')
                .text("You have reviewed and accepted the terms of this license.")
                .append('<img src="images/valide.png" />');

        } else {
            $('#approval-status-2').text('Please review and approve the terms of this license.');
        }

        // refresh current state
        install.stateChange();
    });
});
