define(['jquery', 'lodash', 'ui/validator'], function($, _){

    /**
     * Register a plugin to validate a group of elements
     * 
     * @example $('form').groupValidator();
     * @exports validator/jquery.groupvalidator
     * 
     * @param {Object} options - the plugin options
     * @param {string} [options.errorClass = 'error'] - the class added to the element itself if the validation fails
     * @param {string} [options.errorMessageClass = 'validate-error'] - the class added to the inserted node that contains the failure message itself
     * @param {string} [options.validateOnInit = false] - trigger validation upon initialization
     * @param {string|Array} [options.events = ['change', 'blur']] - the default event that triggers the validation
     * @param {function} [options.callback] - the default callback function triggered after validation.
     * @fires validated.group
     * @returns {jQueryElement} for chaining
     */
    $.fn.groupValidator = function(options){

        options = _.defaults(options || {}, $.fn.groupValidator.defaults);

        return this.each(function(){

            var $container = $(this);
            var states = [];
            var callback = function(valid, results){

                var $elt = $(this);

                //update global state
                states[$elt.attr('name')] = valid;

                //call custom callback
                options.callback.call(this, valid, results, options);

                //trigger event on single validation
                $elt.trigger('validated.single', [valid]);

                /**
                 * Gives the validation state of the entire group. 
                 * Fired at each validation
                 * @event validated.group
                 * @param {boolean} isValid - wheter the group is valid 
                 */
                $container.trigger('validated.group', [_(states).values().contains(false) === false, this]);
            };

            var $toValidate = $('[data-validate]', $container).validator({
                event : options.events,
                validated : callback
            });

            if(options.validateOnInit){
                $toValidate.validator('validate', {}, callback);
            }

        });
    };

    $.fn.groupValidator.defaults = {
        validateOnInit : false,
        errorClass : 'error',
        errorMessageClass : 'validate-error',
        events : ['change', 'blur'],
        callback : function(valid, results, options){

            var $elt = $(this), rule;
            //removes previous error messages
            $elt.siblings('.' + options.errorMessageClass).remove();

            if(valid === false){
                rule = _.where(results, {type : 'failure'})[0];
                $elt.addClass(options.errorClass);
                if(rule && rule.data.message){
                    $elt.after("<span class='" + options.errorMessageClass + "'>" + rule.data.message + "</span>");
                }
            }else{
                $elt.removeClass(options.errorClass);
            }
        }
    };

});
