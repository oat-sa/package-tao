define([
    'core/validator/validators',
    'lodash',
    'i18n',
    'taoQtiItem/qtiItem/core/Element'
], function(validators, _, __, Element) {

    var _qtiIdPattern = /^[_a-zA-Z]{1}[a-zA-Z0-9\-._]{0,31}$/i;

    var qtiValidators = [
        {
            name: 'qtiIdentifier',
            message: __('invalid identifier'),
            validate: function(value, callback) {
                if (typeof callback === 'function') {
                    callback(_qtiIdPattern.test(value));
                }
            }
        },
        //warning: simplistic implementation, allow only one unique identifier in the item no matter the element class/type
        {
            name: 'availableIdentifier',
            message: __('this identifier is already in use'),
            validate: function(value, callback, options) {
                if (options.serial) {
                    var element = Element.getElementBySerial(options.serial);
                    if (element && typeof callback === 'function') {
                        var ids = element.getRelatedItem().getUsedIdentifiers();
                        var available = (!ids[value] || ids[value].serial === element.serial);
                        callback(available);
                    }
                }else{
                    throw 'missing required option "serial"';
                }
            }
        }
    ];

    _.each(qtiValidators, function(rule) {
        validators.register(rule.name, rule);
    });

    return validators;
});

