
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
     * Defines the validations rules through passwordReset.methods()
     * @type {Object}
     */

    var passwordReset = {

        /**
         * Some default values
         * @type {Object}
         * @private
         */

        defaults : {
            rules : {
                 minLength : 4,
                 lower: true
             },
            messages: {
                emptyField : "Please fill the field"
            },
            password : {
                message : "The password is not valid",
                displayPass : false
            }
        },

        setMin : function setMin (minLength) {
            if(minLength === undefined){
                minLength = passwordReset.defaults.rules.minLength;
                return passwordReset.defaults.rules.minLength;
            } else {
                return passwordReset.defaults.rules.minLength = minLength;
            }
        },

        isPasswordValid : function isPasswordValid() {
            return $(this).val().length > passwordReset.defaults.rules.minLength;
        },

        arePasswordsMatching : function arePasswordsMatching($password, $passwordConfirmation) {
            return $password.val() === $passwordConfirmation.val();
        },

        areFieldsEmpty : function areFieldsEmpty ($password, $passwordConfirmation) {
           return $password.val() === "" && $passwordConfirmation.val() === "";
        }


/*        validateCaps : function () {
            if (value === value.toLowerCase()) {
                return false;
            }
            if (value === value.toUpperCase()) {
                return false;
            }
        },*/


    };

    var passwordResetatory = function passwordResetatory (options) {

        return component(passwordReset, passwordReset.defaults)

            .setTemplate(passwordResetTpl)

            .on('render', function() {
                passwordReset.setMin(5);


                this.controls = {
                    $password: this.$component.find('.password'),
                    $passwordConfirmation: this.$component.find('.confirmation-password'),
                    $validationButton: this.$component.find('.validation-button')
                    //$passwordInputs : this.$component.find('input[type="password"]')
                };

                var $button = this.controls.$validationButton,
                    $password = this.controls.$password,
                    $passwordConfirmation = this.controls.$passwordConfirmation;



                $button.on('click', function(e){
                    if (passwordReset.areFieldsEmpty($password, $passwordConfirmation)) {
                        e.preventDefault();
                        //$(this).attr('disabled', true);
                    }
                });

                $password.on('input', function(){
                    console.log(passwordReset.isPasswordValid.call($(this)));
                });

                $passwordConfirmation.on('blur', function(){
                    if(passwordReset.arePasswordsMatching($password, $passwordConfirmation)){
                        console.log('reussi');
                    } else {
                        console.log('byebye');
                    }
                })


            })

            .init(options)

    };

    return passwordResetatory;

});
