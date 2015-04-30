/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'lodash', 'lib/uuid', 'layout/actions/binder', 'layout/actions/common'], function($, _, uuid, binder, commonActions){

    /**
     * The data context for actions
     * @typedef {Object} ActionContext
     * @property {String} [uri] - the resource uri
     * @property {String} [classUri] - the class uri
     */

    /**
     * @exports layout/actions
     */
    var actionManager = {

        /**
         * the found actions, the key is the DOM id
         * @type Object
         */
        _actions : {},

        /**
         * contains the current data context: uri, classUri, both or none
         * @type ActionContext
         */
        _resourceContext : {},

        /**
         * Initialize the actions for the given scope. It should be done only once.
         * @constructor
         * @param {jQueryElement} [$scope = $(document)] - to scope the actions into the page
         */
        init: function init($scope){

            if($scope && $scope.length){
                this.$scope = $scope;
            } else {
                this.$scope = $(document);
            }

            //initialize the registration of common actions
            commonActions();

            this._lookup();
            this.updateContext();
            this._listenUpdates();
            this._bind();
        },

        /** 
         * Lookup for existing actions in the page and add them to the _actions property
         * @private
         */
        _lookup : function _lookup(){
            var self = this;
            $('.action-bar .action', this.$scope).each(function(){
    
                var $this = $(this);
                var id;
                if($this.attr('title')){
                
                    //use the element id
                    if($this.attr('id')){
                        id = $this.attr('id');
                    } else {
                        //or generate one
                        do {
                            id = 'action-' + uuid(8, 16);
                        } while (self._actions[id]);

                        $this.attr('id', id);
                    }

                    self._actions[id] = {
                        id      : id,
                        name    : $this.attr('title'),
                        binding : $this.data('action'),
                        url     : $('a', $this).attr('href'),
                        context : $this.data('context'),
                        state : {
                            disabled    : $this.hasClass('disabled'),
                            hidden      : $this.hasClass('hidden'),
                            active      : $this.hasClass('active')
                        }
                    };
                }
            });
        },

        /** 
         * Bind actions' events: try to execute the binding registered for this action.
         * The behavior depends on the binding name of the action.
         * @private
         */
        _bind : function _bind(){
            var self = this;
            var actionSelector = this.$scope.selector + ' .action-bar .action';

            $(document)
              .off('click', actionSelector) 
              .on('click', actionSelector, function(e){
                e.preventDefault();
                var selected  = self._actions[$(this).attr('id')];
                if(selected && selected.state.disabled === false &&  selected.state.hidden === false){ 
                    self.exec(selected);
                }
            });
        }, 

        /**
         * Listen for event that could update the actions. 
         * Those events may change the current context.
         * @private
         */
        _listenUpdates : function _listenUpdates(){
            var self = this;
            var treeSelector = this.$scope.selector + ' .tree';  
 
            //listen for tree changes
            $(document)
              .off('change.taotree.actions', treeSelector)
              .on('change.taotree.actions', treeSelector, function(e, context){
                context = context || {};
                context.tree = this;
                self.updateContext(context);
            });
        },

        /**
         * Update the current context. Context update may change the visibility of the actions.
         * @param {ActionContext} context - the new context
         */
        updateContext : function updateContext(context){
            var self = this;
            var current;
            var permissions;
 
            context = context || {};
            current = context.uri ? 'instance' : context.classUri ? 'class' : 'none'; 
            permissions = context.permissions || {};
            
            this._resourceContext = _.omit(context, 'permissions');

            _.forEach(this._actions, function(action, id){
                var permission = permissions[action.id];
        
                if( permission === false ||
                    (current === 'none' && action.context !== '*') || 
                    (action.context !== '*' && action.context !== 'resource' && current !== action.context) ){

                    action.state.hidden = true;

                } else {
                    action.state.hidden = false;
                }
                self.updateState();
            });
        },

        /**
         * Update the state of the actions regarding the values of their state property
         */
        updateState : function updateState(){
            _.forEach(this._actions, function(action, id){
                var $elt = $('#' + id);
                _.forEach(['hidden', 'disabled', 'active'], function(state){
                    if(action.state[state] === true){
                        $elt.addClass(state);
                    } else {
                        $elt.removeClass(state);
                    }
                });
            });
        },

        /**
         * Execute the operation bound to an action (via {@link layout/actions/binder#register}); 
         * @param {String|Object} action - can be either the id, the name or the action directly
         * @param {ActionContext} [context] - an action conext, use the current otherwise
         */
        exec : function(action, context){
            if(_.isString(action)){
                if(_.isPlainObject(this._actions[action])){
                    //try to find by id
                    action = this._actions[action];
                } else {
                    //or by by name
                    action = _.find(this._actions, {name : action});
                }
            }
            if(_.isPlainObject(action)){

                //make the executed action active                
                _.forEach(this._actions, function(otherAction){
                    otherAction.state.active = false;
                }); 
                action.state.active = true; 
                this.updateState();

                binder.exec(action, context || this._resourceContext);
            }
        },

        /**
         * Helps you to retrieve an action from it's name or id 
         * @param {String} actionName - name or id of the action
         * @returns {Object} the action
         */
        getBy : function(actionName){
            var action;
            if(_.isPlainObject(this._actions[actionName])){
                //try to find by id
                action = this._actions[actionName];
            } else {
                //or by by name
                action = _.find(this._actions, {name : actionName});
            }
            return action;
        }
    };
    
    return actionManager;
});
