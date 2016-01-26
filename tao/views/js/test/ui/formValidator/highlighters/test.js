define(['lodash', 'jquery', 'ui/formValidator/highlighters/highlighter'], function(_, $, Highlighter) {
    'use strict';

    var highlighter,
        message;

    QUnit.module("Message highlighter", {
        setup: function() {
            highlighter = new Highlighter({
                type : 'message',
                errorClass : 'testErrorClass',
                errorMessageClass : 'testErrorMessageClass'
            });
            message = 'highlight (message)';
        },
        teardown: function() {
            highlighter.destroy($('#field_1'));
        }
    });
    QUnit.test('highlight (message)', function(assert) {
        highlighter.highlight($('#field_1'), message);
        assert.equal($('#field_1').next('.testErrorMessageClass').length, 1, 'Highlighted');
        assert.equal($('#field_1').next('.testErrorMessageClass').text(), message, 'Message is correct');
        assert.ok($('#field_1').hasClass('testErrorClass'), 'Field has error class');

        highlighter.highlight($('#field_1'), message);
        assert.equal($('#field_1').next('.testErrorMessageClass').length, 1, 'Highlighted (message is not duplicated)');
    });
    QUnit.test('uhighlight (message)', function(assert) {
        highlighter.highlight($('#field_1'), message);
        highlighter.unhighlight($('#field_1'));
        assert.equal($('#field_1').next('.testErrorMessageClass').length, 0, 'Unhighlighted');
        assert.ok(!$('#field_1').hasClass('testErrorClass'), 'Field has no error class');
    });


    QUnit.module("Tooltip highlighter", {
        setup: function() {
            highlighter = new Highlighter({
                type : 'tooltip',
                errorClass : 'testErrorClass',
                tooltip : {
                    delay : 0,
                    speed : 0,
                    interactiveTolerance : 0,
                    animation : false,
                    timer : 0
                }
            });
            message = 'highlight (tooltip)';
        },
        teardown: function() {
            highlighter.destroy($('#field_1'));
        }
    });
    QUnit.test('highlight (tooltip)', function(assert) {
        highlighter.highlight($('#field_1'), message);
        assert.ok($('#field_1').hasClass('testErrorClass'), 'Field has error class');
        assert.equal($('.tao-error-tooltip').length, 1, 'Highlighted (tooltip is rendered)');
        assert.equal($('.tao-error-tooltip .tooltipster-content').text(), message, 'Message is correct');
    });
    QUnit.test('unhighlight (tooltip)', function(assert) {
        highlighter.highlight($('#field_1'), message);
        highlighter.unhighlight($('#field_1'));
        assert.ok(!$('#field_1').hasClass('testErrorClass'), 'Field has no error class');
        assert.equal($('.tao-error-tooltip').length, 0, 'Unhighlighted tooltip is removed');
    });
});
