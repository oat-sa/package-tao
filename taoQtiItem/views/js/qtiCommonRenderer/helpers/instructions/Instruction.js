define([
    'lodash',
    'taoQtiItem/qtiItem/helper/util',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/instruction',
], function(_, util, instructionTpl){

    var _notificationLevels = ['info', 'warning', 'error', 'success'];

    var Instruction = function(interaction, message, callback){
        this.interaction = interaction;
        this.defaultMessage = message || '';
        this.currentMessage = '';
        this.level = 'info';
        this.serial = util.buildSerial('instruction_');
        this.callback = callback;
        this.$dom = null;
        this.state = false;
    };

    Instruction.isValidLevel = function(level){
        return (_.indexOf(_notificationLevels, level) >= 0);
    };

    Instruction.prototype.setState = function(state){
        this.state = state;
    };

    Instruction.prototype.checkState = function(state){
        return (this.state === state);
    };

    Instruction.prototype.getId = function(){
        return this.serial;
    };

    Instruction.prototype.create = function($container){

        $container.append(instructionTpl({
            'message' : this.defaultMessage,
            'serial' : this.serial
        }));

        this.$dom = $container.find('#' + this.serial);
    };

    Instruction.prototype.update = function(options){

        var level = (options && options.level) ? options.level : '',
            message = (options && options.message) ? options.message : '',
            timeout = (options && options.timeout) ? options.timeout : 0,
            start = (options && typeof(options.start) === 'function') ? options.start : null,
            stop = (options && typeof(options.stop) === 'function') ? options.stop : null;

        if(level && Instruction.isValidLevel(level)){
            this.$dom.removeClass('feedback-' + this.level).addClass('feedback-' + level);
            this.$dom.find('.icon').removeClass('icon-' + this.level).addClass('icon-' + level);
            this.level = level;
        }

        if(message){
            this.$dom.find('.instruction-message').html(message);
            this.currentMessage = message;
        }

        if(timeout){
            var _this = this;
            if(start){
                start.call(_this);
            }
            _this.timer = setTimeout(function(){
                if(stop){
                    stop.call(_this);
                }
                _this.timer = null;
            }, timeout);
        }

    };

    Instruction.prototype.setLevel = function(level, timeout){
        var options = {
            level : level
        };
        
        if(timeout){
            options.timeout = parseInt(timeout);
            options.stop = function(){
                this.setLevel('info');
            };
        }
        
        this.update(options);
    };
    
    Instruction.prototype.getLevel = function() {
        return this.level;
    };

    Instruction.prototype.setMessage = function(message, timeout){
        this.update({message : message, timeout : timeout});
    };

    Instruction.prototype.reset = function(){
        this.update({level : 'info', message : this.defaultMessage});
        this.state = false;
    };

    Instruction.prototype.validate = function(data){
        if(typeof(this.callback) === 'function'){
            this.callback.call(this, data);
        }
    };

    return Instruction;
});
