define([
    'layout/actions/binder',
    'uri',
    'jquery',
    'context',
    'helpers',
    'taoBackOffice/treeRender'
], function (binder, uri, $, context, helpers, treeRender) {
    'use strict';

    /**
     *
     * @type {{start: Function}}
     */
    var itemRunnerController = {

        //the controller initialization
        start: function () {
        	
        	$('.tree-container').each(function(i, obj) {
        		var $container = $(obj);
                var uri = $container.data('id');

                $.post(helpers._url('getTree', 'Trees', 'taoBackOffice'), {uri: uri}, function (treeData) {

                    var $parent = $container.closest('.content-block');

                    var resizeContainer = function () {
                        $container.height($parent.height() - $parent.find('.panel').eq(0).outerHeight());
                        $container.width($parent.width());
                    };

                    $(window).on('resize', resizeContainer);

                    resizeContainer();
                    treeRender.init($container[0], treeData);
                    treeRender.run();

                });
        	});
        }
    };

    // the controller is exposed
    return itemRunnerController;
});