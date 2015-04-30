/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'lodash',
    'context',
    'layout/section',
    'layout/actions/binder',
    'layout/search',
    'layout/filter',
	'uri',
	'ui/feedback'
], function($, __, _, appContext, section, binder, search, toggleFilter, uri, feedback){
    'use strict';

    /**
     * Register common actions.
     *
     * TODO this common actions may be re-structured, split in different files or moved in a more obvious location.
     *
     * @exports layout/actions/common
     */
    var commonActions = function(){

        /**
         * Register the load action: load the url and into the content container
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} [actionContext.uri]
         * @param {String} [actionContext.classUri]
         */
        binder.register('load', function load(actionContext){
            section.current().loadContentBlock(this.url, _.pick(actionContext, ['uri', 'classUri', 'id']));
        });

        /**
         * Register the load class action: load the url into the content container
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} actionContext.classUri - the URI of the parent class
         */
        binder.register('loadClass', function load(actionContext){
            section.current().loadContentBlock(this.url, {classUri: actionContext.classUri, id: uri.decode(actionContext.classUri)});
        });

        /**
         * Register the subClass action: creates a sub class
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} actionContext.classUri - the URI of the parent class
         *
         * @fires layout/tree#addnode.taotree
         */
        binder.register('subClass', function subClass(actionContext){
            var classUri = uri.decode(actionContext.classUri);
            $.ajax({
                url: this.url,
                type: "POST",
                data: {classUri: actionContext.classUri, id: classUri, type: 'class'},
                dataType: 'json',
                success: function(response){
                    if (response.uri) {
                        $(actionContext.tree).trigger('addnode.taotree', [{
                            'uri'       : uri.decode(response.uri),
                            'parent'    : classUri,
                            'label'     : response.label,
                            'cssClass'  : 'node-class'
                        }]);
                    }
                }
            });
        });

        /**
         * Register the instanciate action: creates a new instance from a class
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} actionContext.classUri - the URI of the class' instance
         *
         * @fires layout/tree#addnode.taotree
         */
        binder.register('instanciate', function instanciate(actionContext){
            var classUri = uri.decode(actionContext.classUri);
            $.ajax({
                url: this.url,
                type: "POST",
                data: {classUri: actionContext.classUri, id: classUri, type: 'instance'},
                dataType: 'json',
                success: function(response){
                    if (response.uri) {
                        $(actionContext.tree).trigger('addnode.taotree', [{
                            'uri'		: uri.decode(response.uri),
                            'parent'    : classUri,
                            'label'     : response.label,
                            'cssClass'  : 'node-instance'
                        }]);
                    }
                }
            });
        });

        /**
         * Register the duplicateNode action: creates a clone of a node.
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} actionContext.uri - the URI of the base instance
         * @param {String} actionContext.classUri - the URI of the class' instance
         *
         * @fires layout/tree#addnode.taotree
         */
        binder.register('duplicateNode', function duplicateNode(actionContext){
            $.ajax({
                url: this.url,
                type: "POST",
                data: {uri: actionContext.id, classUri: uri.decode(actionContext.classUri)},
                dataType: 'json',
                success: function(response){
                    if (response.uri) {
                        $(actionContext.tree).trigger('addnode.taotree', [{
                            'uri'       : uri.decode(response.uri),
                            'parent'    : uri.decode(actionContext.classUri),
                            'label'     : response.label,
                            'cssClass'  : 'node-instance'
                        }]);
                    }
                }
            });
        });

        /**
         * Register the removeNode action: removes a resource.
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} [actionContext.uri]
         * @param {String} [actionContext.classUri]
         *
         * @fires layout/tree#removenode.taotree
         */
        binder.register('removeNode', function remove(actionContext){
            var data = {
                uri: uri.decode(actionContext.uri),
                classUri: uri.decode(actionContext.classUri),
                id: actionContext.id
            };
            //TODO replace by a nice popup
            if (window.confirm(__("Please confirm deletion"))) {
                $.ajax({
                    url: this.url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function(response){
                        if (response.deleted) {
                            $(actionContext.tree).trigger('removenode.taotree', [{
                                id : actionContext.uri || actionContext.classUri
                            }]);
                        } else {
                            var msg = response.msg || __("Unable to delete the selected resource");
                            feedback().error(msg);
                        }
                    }
                });
            }
        });

        /**
         * Register the moveNode action: moves a resource.
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} [actionContext.uri]
         * @param {String} [actionContext.classUri]
         */
        binder.register('moveNode', function remove(actionContext){
            var data = _.pick(actionContext, ['id', 'uri', 'destinationClassUri', 'confirmed']);

            //wrap into a private function for recusion calls
            var _moveNode = function _moveNode(url, data){
                $.ajax({
                    url: url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function(response){

                        if (response && response.status === 'diff') {
                            var message = __("Moving this element will replace the properties of the previous class by those of the destination class :");
                            message += "\n";
                            for (var i = 0; i < response.data.length; i++) {
                                if (response.data[i].label) {
                                    message += "- " + response.data[i].label + "\n";
                                }
                            }
                            message += __("Please confirm this operation.") + "\n";

                            if (window.confirm(message)) {
                                data.confirmed = true;
                                return  _moveNode(url, data);
                            }
                          } else if (response && response.status === true) {
                                //open the destination branch
                                $(actionContext.tree).trigger('openbranch.taotree', [{
                                    id : actionContext.destinationClassUri
                                }]);
                                return;
                          }

                          //ask to rollback the tree
                          $(actionContext.tree).trigger('rollback.taotree');
                    }
                });
            };
            _moveNode(this.url, data);
        });

        /**
         * This action helps to filter tree content.
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         */
        binder.register('filter', function filter(actionContext){
            $('#panel-' + appContext.section + ' .search-form').slideUp();

            toggleFilter($('#panel-' + appContext.section + ' .filter-form'));
        });

        /**
         * Register the removeNode action: removes a resource.
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} [actionContext.uri]
         * @param {String} [actionContext.classUri]
         */
        binder.register('launchFinder', function remove(actionContext){


            var data = _.pick(actionContext, ['uri', 'classUri', 'id']);
	            // used to avoid same query twice
	        var uniqueValue = data.uri || data.classUri || '';
	        var $container  = $('.search-form [data-purpose="search"]');

            $('.filter-form').slideUp();

            if($container.is(':visible')){
                $('.search-form').slideUp();
                search.reset();
                return;
            }

            if($container.data('current') === uniqueValue) {
                $('.search-form').slideDown();
                return;
            }

            $.ajax({
                url: this.url,
                type: "GET",
                data: data,
                dataType: 'html'
            }).done(function(response){
                $container.data('current', uniqueValue);
                search.init($container, response);
                $('.search-form').slideDown();
            });
        });


        /**
         * Register the launchEditor action.
         *
         * @this the action (once register it is bound to an action object)
         *
         * @param {Object} actionContext - the current actionContext
         * @param {String} [actionContext.uri]
         * @param {String} [actionContext.classUri]
         *
         * @fires layout/tree#removenode.taotree
         */
        binder.register('launchEditor', function launchEditor(actionContext){

            var data = _.pick(actionContext, ['id']);
            var wideDifferenciator = '[data-content-target="wide"]';

            $.ajax({
                url: this.url,
                type: "GET",
                data: data,
                dataType: 'html',
                success: function(response){
                    var $response = $(response);
                    //check if the editor should be displayed widely or in the content area
                    if($response.is(wideDifferenciator) || $response.find(wideDifferenciator).length){
                        section.create({
                            id : 'authoring',
                            name : __('Authoring'),
                            url : this.url,
                            content : $response,
                            visible : false
                        })
                        .show();
                    } else {
                       section.updateContentBlock($response);
                    }
                }
            });
        });
    };

    return commonActions;
});


