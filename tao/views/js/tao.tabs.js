/**
 * TaoTabsClass is an easy to use container for the jquery tabs widget,
 * It provides tools to adapt style & behavior to your needs
 *
 * @example new TaoTabsClass('#tabs-container', {});
 * @see TaoTabsClass.defaultOptions for options example
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 *
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoTabsClass constructor
 * @param {String} selector the jquery selector of the tree container
 * @param {Object} options {formContainer, actionId, instanceClass, instanceName, selectNode,
 * 							editClassAction, editInstanceAction, createInstanceAction,
 * 							moveInstanceAction, subClassAction, deleteAction, duplicateAction}
 */
function TaoTabsClass(selector, options) {
	if (!options) {
		options = TaoTabsClass.defaultOptions;
	}
	this.selector = selector;

	//$(this.selector).hide();
	$(this.selector).tabs();
	$(this.selector).find(".ui-tabs-nav, .ui-tabs-nav > *")
		.removeClass("ui-corner-all ui-corner-top")
		.addClass("ui-corner-bottom");
}

/**
 * TaoTabs default options
 */
TaoTabsClass.defaultOptions = {
	'position' : 'top'		// top, bottom
};