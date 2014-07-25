require.config({
	baseUrl: taobase_www + 'js',
	paths: {
		jqueryUI: [
			'jquery-ui-1.8.23.custom.min'
		],
                "taoQtiTest" : '../../../taoQtiTest/views/js'
	},
	shim: {
		'jqueryUI': ['jquery'],
		'jsTree/plugins/jquery.tree.contextmenu': ['jsTree/jquery.tree'],
		'jsTree/plugins/jquery.tree.checkbox': ['jsTree/jquery.tree'],
		'generis.tree.select': ['generis.tree', 'jsTree/plugins/jquery.tree.checkbox'],
		'generis.tree.browser': ['generis.tree', 'jsTree/plugins/jquery.tree.contextmenu'],
		'grid/tao.grid': ['jquery.jqGrid-4.4.0/js/jquery.jqGrid.min', 'jquery.jqGrid-4.4.0/js/i18n/grid.locale-'+base_lang],
		'grid/tao.grid.downloadFileResource': ['grid/tao.grid'],
		'grid/tao.grid.rowId': ['grid/tao.grid'],
		'AsyncFileUpload': ['jquery.uploadify/swfobject', 'jquery.uploadify/jquery.uploadify.v2.1.4.min']
	}
});

var callbackMeWhenReady = {};

var helpers;
var uiBootstrap;
var eventMgr;
var uiForm;
var generisActions;

require(['require', 'jquery', 'class', 'uiBootstrap', 'helpers', 'EventMgr', 'uiForm', 'generis.actions', 'jqueryUI', 'i18n'], function (req, $, Class, UiBootstrap, Helpers, EventMgr, UiForm, GenerisActions) {
	$(function(){
		helpers = new Helpers();
		uiBootstrap = new UiBootstrap();
		eventMgr = new EventMgr();
		uiForm = new UiForm();
		generisActions = new GenerisActions();
		for (var e in callbackMeWhenReady) {
			callbackMeWhenReady[e]();
		}
	});
});