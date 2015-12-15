
/**
 * Build a password reset component
 * @author Georges Mbella <georges@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'ui/component',
    'tpl!taoMpArt/components/passwordReset/passwordReset'
], function ($, __, component, passwordResetTpl) {

    /**
     * Creates a set of password input fields. One will be receiving the old Pass, the next one will hold the new pass, and the last will confirm the new pass.
     * @param {jQueryElement} $contaier - the div element to append actions to
     * @returns {passwordReset} the component
     */

    return function passwordResetFactor(options) {
        return component({})
            .setTemplate(passwordResetTpl)

            // renders the component
            .on('render', function render() {
               // here will be the logic for checking all the input checkings

                // define any needed variables
                var $container = $('#password-resetter'),
                    $containerElements = $container.find('input'),
                    $oldPass = $('#oldPass'),
                    $password = $('#password'),
                    $passVal = $password.val(),
                    $confPass = $('#conf-password'),
                    $confPassVal = $confPass.val();
                    $validateButton = $('#validation-button');

                options = {
                   isPassVisible : false
                };

                // then initialize the component

                // Provide possibility for the user to see the password he typed
                // we will set a checkbox which if checked, will switch the password input type to text so the user can see his password. We will access the options object


                // Check that the passwords are 4 characters or more
                var validateCharsLength = function validateCharsLength () {
                    var value = $(this).val();
                    var valid = value.length >= 4;
                    if (!valid) {
                        //setErrorMessage(password, 'Please make sure your password has at least 4 characters');
                    }
                    return valid;
                };

                var validateEquality = function validateEquality () {
                    var valid = ($passVal == $confPass);
                    if (valid && $passVal !='' && $confPassVal !='') {
                        // we allow to submit
                    }
                };


                // Event handling

                $containerElements.on('input',  validateCharsLength);
                $confPass.on('blur', validateEquality);

                return {
                    validateCharsLength : validateCharsLength,
                    validateEquality : validateEquality
                }

            })

            .init(options);
    };


});
