define(['lodash', 'jquery', 'ui/validator'], function(_, $, FormValidator) {
    'use strict';

    QUnit.test('validate', function(assert) {
        assert.ok(true, 'Form is valid');
        $('#field_0').validator();
        $('#field_1').validator();
        $('#field_2').validator();

        assert.equal($('#field_0').data('validator-instance').rules[0].name, 'notEmpty');
        assert.equal($('#field_1').data('validator-instance').rules[0].name, 'notEmpty');
        assert.equal($('#field_2').data('validator-instance').rules[0].name, 'notEmpty');

        assert.equal($('#field_0').data('validator-instance').rules[1].name, 'pattern');
        assert.equal($('#field_1').data('validator-instance').rules[1].name, 'pattern');
        assert.equal($('#field_2').data('validator-instance').rules[1].name, 'pattern');

        assert.equal($('#field_0').data('validator-instance').rules[1].options.pattern, '[A-Z][a-z]{3,}');
        assert.equal($('#field_0').data('validator-instance').rules[1].options.modifier, 'i');

        assert.equal($('#field_1').data('validator-instance').rules[1].options.pattern, '[A-Z][a-z]{3,}');
        assert.equal($('#field_1').data('validator-instance').rules[1].options.modifier, 'i');
        assert.equal($('#field_1').data('validator-instance').rules[1].options.message, 'abcd, key=val, foo(bar)');

        assert.equal($('#field_2').data('validator-instance').rules[1].options.pattern, '[A-Z][a-z]{3,}');
        assert.equal($('#field_2').data('validator-instance').rules[1].options.modifier, 'i');
        assert.equal($('#field_2').data('validator-instance').rules[1].options.message, 'abcd key=val foo(bar)');
    });
});
