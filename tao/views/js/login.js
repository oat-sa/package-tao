/**
 * Set focus on the login field.
 */
function focusFirstField() {
	$('input[name="login"]').focus();
}

require(['require', 'jquery'], function(req, $) {
	$(function() {
		focusFirstField();
	});
});