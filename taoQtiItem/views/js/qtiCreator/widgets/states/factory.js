define(['jquery', 'lodash'], function($, _){

    var _isValidStateDefinition = function(state){

        if(!_.isFunction(state)){
            return false;
        }

        if(!state.prototype.name || !state.prototype.superState){
            return false;
        }

        if(!_.isFunction(state.prototype.init) || !_.isFunction(state.prototype.exit)){
            return false;
        }

        return true;
    };


    var _create = function(name, superStates, init, exit){

        var State = function(widget){
            this.widget = widget;
        };

        State.prototype.name = name;

        State.prototype.superState = superStates || [];

        if(init && exit){
            State.prototype.init = function(){

                var $container = this.widget.$container;

                if($container.data('edit') === name){
                    $container.show();
                }

                var $editableWidgets = $container.find('[data-edit="' + name + '"]').filter(function(){
                    var $parentWidget = $(this).closest('.widget-box');
                    if($parentWidget.length && $parentWidget[0] === $container[0]){
                        return true;
                    }
                    return false;
                });
                $editableWidgets.show();

                this.widget.$container.addClass('edit-' + name);
                $(document).trigger('beforeStateInit.qti-widget', [this.widget.element, this]);
                init.call(this);
                $(document).trigger('afterStateInit.qti-widget', [this.widget.element, this]);
            };
            State.init = init;//store reference for future usage

            State.prototype.exit = function(){
                if(this.widget.$container.data('edit') === name){
                    this.widget.$container.hide();
                }
                this.widget.$container.find('[data-edit="' + name + '"]').hide();
                this.widget.$container.removeClass('edit-' + name);

                $(document).trigger('beforeStateExit.qti-widget', [this.widget.element, this]);

                //remove any qti element change event attached during this state lifetime
                $(document).off(['.qti-widget', name, this.widget.serial].join('.'));

                exit.call(this);
                $(document).trigger('afterStateExit.qti-widget', [this.widget.element, this]);
            };
            State.exit = exit;//store reference for future usage
        }

        return State;
    };

    return {
        /**
         * Create qti widget state from scratch or by using an existing state as model
         * When creating a state from scratch, the name, the init() and exit() callback are required.
         * The optional superStates (array) is optional and is useful only when you need to define a hierarchy between the new state
         * abd the ones in the superStates array.
         * When the widget state is changed in this newly created state, the init()
         *
         * accepts the following arguments:
         * (string) stateName, (array) superStates, (function) init, (function) exit
         * (string) stateName, (function) init, (function) exit
         * (function) State, (function) init, (function) exit
         * @returns {State}
         */
        create : function(){

            var State, name, superStates, init, exit;

            if(_.isString(arguments[0])){
                name = arguments[0];
                if(_.isArray(arguments[1])){
                    superStates = arguments[1];
                    if(_.isFunction(arguments[2]) && _.isFunction(arguments[3])){
                        init = arguments[2];
                        exit = arguments[3];
                        State = _create(name, superStates, init, exit);
                    }else{
                        throw new Error('the third and fourth arguments are expected to be functions: init() & exit()');
                    }
                }else{
                    superStates = [];
                    if(_.isFunction(arguments[1]) && _.isFunction(arguments[2])){
                        init = arguments[1];
                        exit = arguments[2];
                        State = _create(name, superStates, init, exit);
                    }else{
                        throw new Error('the second and third arguments are expected to be functions: init() & exit()');
                    }
                }
            }else if(_isValidStateDefinition(arguments[0])){
                name = arguments[0].prototype.name;
                superStates = arguments[0].prototype.superState;
                if(_.isFunction(arguments[1]) && _.isFunction(arguments[2])){
                    init = arguments[1];
                    exit = arguments[2];
                    State = _create(name, superStates, init, exit);
                }else{
                    throw new Error('the second and third arguments are expected to be functions: init() & exit()');
                }
            }else{
                throw new Error('invalid first argument : expected the state name (string) or a State (function)');
            }

            return State;
        },
        extend : function(State, init, exit){

            var Clone = null, initFn, exitFn;

            if(_isValidStateDefinition(State)){

                if(_.isFunction(init)){
                    initFn = function(){
                        State.init.call(this);
                        init.call(this);
                    };
                }
                if(_.isFunction(exit)){
                    exitFn = function(){
                        exit.call(this);
                        State.exit.call(this);
                    };
                }

                Clone = _create(State.prototype.name, State.prototype.superState, initFn, exitFn);
                _.forIn(State.prototype, function(prop, name){
                    if(_.isFunction(prop)){
                        if((initFn && name === 'init') || (exitFn && name === 'exit')){
                            return true;
                        }
                        Clone.prototype[name] = prop;
                        Clone[name] = prop;
                    }
                });
            }else{
                throw new Error('invalid state to be cloned');
            }

            return Clone;
        },
        createBundle : function(arg0, arg1, arg2){

            var stateBundle = {},
                newStates = [],
                excluded = [];

            if(arguments.length >= 2){
                stateBundle = _.clone(arguments[0]);
                newStates = arguments[1];
                excluded = arguments[2] || excluded;
            }else{
                newStates = arguments[0];
            }

            _.each(newStates, function(state){

                if(_isValidStateDefinition(state)){
                    stateBundle[state.prototype.name] = state;
                }
            });

            _.each(excluded, function(state){
                delete stateBundle[state];
            });

            return stateBundle;
        },
        throwMissingRequiredImplementationError : function(functionName){
            throw new Error('Missing required implementation of the function ' + functionName + '()');
        }
    };
});


