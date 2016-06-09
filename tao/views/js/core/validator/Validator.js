define(['lodash', 'async', 'core/validator/Report', 'core/validator/validators'], function(_, async, Report, validators){
    'use strict';

    var _buildRule = function(rule){
        var ret = null;
        var _rules = validators.validators;
        if(_.isString(rule) && _rules[rule]){
            ret = _rules[rule];
        } else if(_.isObject(rule) && rule.name){
            if(_rules[rule.name]){
                ret = _.merge(_.cloneDeep(_rules[rule.name]), rule);
            }else if(rule.message && _.isFunction(rule.validate)){
                ret = rule;
            }
        }
        return ret;
    };

    var _defaultOptions = {
        lazy : false
    };

    var _applyRules = function(value, rule, callback, options){
        options = _.merge(_.cloneDeep(rule.options), options);
        rule.validate(value, callback, options);
    };

    var Validator = function(rules, options){
        this.options = _.merge(_.cloneDeep(_defaultOptions), options);
        this.rules = [];
        this.addRules(rules);
    };

    Validator.getDefaultOptions = function(){
        return _.clone(_defaultOptions);
    };

    Validator.prototype.validate = function(value, arg1, arg2){

        var callstack = [], callback, options = _.cloneDeep(this.options);

        if(_.isFunction(arg1)){
            callback = arg1;
        }else if(_.isObject(arg1)){
            _.merge(options, arg1);//treat it like the options array:
            if(_.isFunction(arg2)){
                callback = arg2;
            }
        }

        _.each(this.rules, function(rule){

            //note: individual validating option reserved for a later usage:
            var validatorOptions = {},
                message;

            callstack.push(function(cb){

                _applyRules(value, rule, function(success){
                    if(success){
                        cb(null, new Report('success', {validator : rule.name}));
                    }else{
                        message = rule.options.message || rule.message;
                        var report = new Report('failure', {validator : rule.name, message : message});
                        if(options.lazy){
                            cb(new Error('lazy mode'), report);//stop execution now
                        }else{
                            cb(null, report);
                        }
                    }

                }, validatorOptions);

            });
        });


        async.series(callstack, function(err, results){
            if(_.isFunction(callback)){
                callback(results);
            }
        });

        return this;
    };

    Validator.prototype.addRule = function(rule){
        var _rules = validators.validators;
        if(_.isString(rule) && _rules[rule]){
            this.rules.push(_rules[rule]);
        } else if(rule = _buildRule(rule)){
            this.rules.push(rule);
        }
        return this;
    };

    Validator.prototype.addRules = function(rules){
        var _this = this;
        _.each(rules, function(rule){
            _this.addRule(rule);
        });
        return this;
    };

    return Validator;
});
