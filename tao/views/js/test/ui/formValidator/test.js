define(['lodash', 'jquery', 'ui/formValidator/formValidator'], function(_, $, formValidator) {
    'use strict';

    //#field_1 - data-validate="$numeric"
    //#field_2 - data-validate="$notEmpty"
    //#field_3 - data-validate="$pattern(pattern=\w{3}\s\w{3}\sbaz, modifier=i)"
    //#field_5 - data-validate="$length(min=4, max=10)"
    //#field_6 - should be ignored
    //#field_7 - data-validate="$length(min=4, max=10, message='length should be from 4 to 10'); $numeric(message='must be numeric')"

    var casesValid = [{
            '#field_1' : '1',
            '#field_2' : 'str',
            '#field_3' : 'foo bar BAZ',
            '#field_5' : 'qwert',
            '#field_7' : '12345'
        }],
        casesInvalid = [{
            '#field_1' : 'a',
            '#field_2' : '',
            '#field_3' : 'invalid',
            '#field_5' : 'a',
            '#field_7' : 'b'
        }],
        fieldSelector = '[data-validate]:not(.ignore)',
        validator;

    QUnit.module("Form Validator", {
        setup: function() {
            validator = formValidator({
                container : $('#form_1'),
                event : 'change',
                selector : fieldSelector
            });
        },
        teardown: function() {
            validator.destroy();
        }
    });

    QUnit.cases(casesValid)
        .test('validate valid form', function(data, assert) {
            QUnit.expect(1);

            _.forEach(data, function (value, selector) {
                $(selector).val(value);
            });

            assert.ok(validator.validate(), 'Form is valid');
        });

    QUnit.cases(casesInvalid)
        .test('validate valid form', function(data, assert) {
            QUnit.expect(1);

            _.forEach(data, function (value, selector) {
                $(selector).val(value);
            });

            assert.ok(!validator.validate(), 'Form is mot valid');
        });

    QUnit.cases(casesInvalid)
        .test('Highlight fields', function(data, assert) {
            _.forEach(data, function (value, selector) {
                $(selector).val(value);
            });

            validator.validate();
            assert.equal($('#form_1').find('.error').length, $(fieldSelector).length, 'Fields highlighted');
            assert.equal($('#form_1').find('.validate-error').length, $(fieldSelector).length, 'Error messages rendered');
        });

    QUnit.cases(casesValid)
        .test('Unhighlight fields', function(data, assert) {
            _.forEach(data, function (value, selector) {
                $(selector).val(value);
            });

            $('#field_1').val('non numeric');
            validator.validate();
            $('#field_1').val('1');
            validator.validate();
            assert.equal($('#form_1').find('.error').length, 0, 'Fields unhighlighted');
            assert.equal($('#form_1').find('.validate-error').length, 0, 'Error messages removed');
        });

    QUnit.cases(casesInvalid)
        .test('getState invalid', function(data, assert) {
            _.forEach(data, function (value, selector) {
                $(selector).val(value);
            });

            validator.validate();
            assert.ok(validator.getState().valid === false);
            assert.ok(validator.getState().errors.length > 0);
            assert.ok(validator.getState().errors[0].field.length === 1, 'Error filed object is represented in the report');
            assert.ok(!!validator.getState().errors[0].message, 'Error message is represented in the report');
            assert.ok(!!validator.getState().errors[0].validator, 'Validator name is represented in the report');
        });

    QUnit.cases(casesValid)
        .test('getState valid', function(data, assert) {
            _.forEach(data, function (value, selector) {
                $(selector).val(value);
            });

            validator.validate();
            assert.ok(validator.getState().valid);
            assert.ok(validator.getState().errors.length === 0);
        });
});
