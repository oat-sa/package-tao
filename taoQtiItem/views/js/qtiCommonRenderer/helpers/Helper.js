define([
    'lodash',
    'jquery',
    'i18n',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCommonRenderer/helpers/Instruction',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/notification'
], function(_, $, __, Element, Instruction, notifTpl){

    var _containers = {};
    var _instructions = {};
    var _$containerContext = $();

    var _getSelector = function(element){

        var serial = element.getSerial(),
            selector = '[data-serial=' + serial + ']';

        if(Element.isA(element, 'choice')){
            selector = '.qti-choice' + selector;
        }else if(Element.isA(element, 'interaction')){
            selector = '.qti-interaction' + selector;
        }

        return selector;
    };

    return {
        setContext : function($scope){
            _$containerContext = $scope;
        },
        getContainer : function(element, $scope){
            
            var serial = element.getSerial();
            
            if($scope instanceof $ && $scope.length){
                
                //find in the given context
                return $scope.find(_getSelector(element));
                
            }else if(_$containerContext instanceof $ && _$containerContext.length){
                
                //find in the globally set context
                return _$containerContext.find(_getSelector(element));
                
            }else if(!_containers[serial] || _containers[serial].length){
                
                //find in the global context
                _containers[serial] = $(_getSelector(element));
            }

            return _containers[serial];
        },
        validateInstructions : function(element, data){
            var serial = element.getSerial();
            if(_instructions[serial]){
                _.each(_instructions[serial], function(instruction){
                    instruction.validate(data);
                });
            }
        },
        appendInstruction : function(element, message, validateCallback){
            var serial = element.getSerial(),
                instruction = new Instruction(element, message, validateCallback);

            if(!_instructions[serial]){
                _instructions[serial] = {};
            }
            _instructions[serial][instruction.getId()] = instruction;

            instruction.create(this.getContainer(element).find('.instruction-container'));

            return instruction;
        },
        removeInstructions : function(element){
            _instructions[element.getSerial()] = {};
            this.getContainer(element).find('.instruction-container').empty();
        },
        /**
         * Reset the instructions states for an element (but keeps configuration)
         * @param {Object} element - the qti object, ie. interaction, choice, etc.
         */
        resetInstructions : function(element){
            var serial = element.getSerial();
            if(_instructions[serial]){
                _.each(_instructions[serial], function(instruction){
                    instruction.reset();
                });
            }
        },
        /** 
         * Default instuction set with a min/max constraints.
         * @param {Object} interaction
         * @param {Object} options
         * @param {Number} [options.min = 0] - 
         * @param {Number} [options.max = 0] - 
         * @param {Function} options.getResponse - a ref to a function that get the raw response (array) from the interaction in parameter
         * @param {Function} [options.onError] - called by once an error occurs with validateInstruction data in parameters
         */
        minMaxChoiceInstructions : function(interaction, options){

            var self = this,
                min = options.min || 0,
                max = options.max || 0,
                getResponse = options.getResponse,
                onError = options.onError || _.noop(),
                choiceCount = options.choiceCount === false ? false : _.size(interaction.getChoices()),
                minInstructionSet = false,
                msg;

            if(!_.isFunction(getResponse)){
                throw "invalid parameter getResponse";
            }

            //if maxChoice = 0, inifinite choice possible
            if(max > 0 && (choiceCount === false || max < choiceCount)){
                if(max === min){
                    minInstructionSet = true;
                    msg = (max <= 1) ? __('You must select exactly %d choice', max) : __('You must select exactly %d choices', max);

                    self.appendInstruction(interaction, msg, function(data){

                        if(getResponse(interaction).length >= max){
                            this.setLevel('success');
                            if(this.checkState('fulfilled')){
                                this.update({
                                    level : 'warning',
                                    message : __('Maximum choices reached'),
                                    timeout : 2000,
                                    start : function(){
                                        onError(data);
                                    },
                                    stop : function(){
                                        this.update({level : 'success', message : msg});
                                    }
                                });
                            }
                            this.setState('fulfilled');
                        }else{
                            this.reset();
                        }
                    });
                }else if(max > min){
                    msg = (max <= 1) ? __('You can select maximum %d choice', max) : __('You can select maximum %d choices', max);
                    self.appendInstruction(interaction, msg, function(data){

                        if(getResponse(interaction).length >= max){

                            this.setLevel('success');
                            this.setMessage(__('Maximum choices reached'));
                            if(this.checkState('fulfilled')){
                                this.update({
                                    level : 'warning',
                                    timeout : 2000,
                                    start : function(){
                                        onError(data);
                                    },
                                    stop : function(){
                                        this.setLevel('info');
                                    }
                                });
                            }

                            this.setState('fulfilled');
                        }else{
                            this.reset();
                        }
                    });
                }
            }

            if(!minInstructionSet && min > 0 && (choiceCount === false || min < choiceCount)){
                msg = (min <= 1) ? __('You must at least %d choice', min) : __('You must select at least %d choices', max);
                self.appendInstruction(interaction, msg, function(){
                    if(getResponse(interaction).length >= min){
                        this.setLevel('success');
                    }else{
                        this.reset();
                    }
                });
            }
        },
        appendNotification : function(element, message, level){

            level = level || 'info';

            if(Instruction.isValidLevel(level)){

                var $container = this.getContainer(element);

                $container.find('.notification-container').prepend(notifTpl({
                    'level' : level,
                    'message' : message
                }));

                var $notif = $container.find('.item-notification:first');
                var _remove = function(){
                    $notif.fadeOut();
                };

                $notif.find('.close-trigger').on('click', _remove);
                setTimeout(_remove, 2000);

                return $notif;
            }
        },
        removeNotifications : function(element){
            this.getContainer(element).find('.item-notification').remove();
        },
        triggerResponseChangeEvent : function(interaction, extraData){
            this.getContainer(interaction).trigger('responseChange', [{
                    interaction : interaction,
                    response : interaction.getResponse()
                },
                extraData
            ]);
        },
        targetBlank : function($container){
            
            $container.on('click', 'a', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                if(href && href.match(/^http/i)){
                    window.open(href, '_blank');
                }
            });
        }
    };
});
