define([
    'lodash',
    'jquery',
    'mathJax',
    'ui/feedback'
], function(_, $, MathJax, feedback){
    "use strict";
    var MathEditor = function MathEditor(config){

        config = config || {};

        this.mathML = config.mathML || '';
        this.tex = config.tex || '';
        this.display = config.display || 'inline';

        //computed, system variables:
        this.processing = false;
        this.$target = config.target || $();

        if(config.buffer && config.buffer instanceof $ && config.buffer.length){
            this.$buffer = config.buffer;
        }else{
            throw 'missing required element in config "buffer"';
        }
    };

    MathEditor.prototype.setMathML = function(mathMLstr){

        this.mathML = _stripMathTags(mathMLstr);

        return this;//for chaining purpose
    };

    MathEditor.prototype.setTex = function(texStr){

        this.tex = texStr;

        //need to run renderFromTex

        return this;//for chaining purpose
    };

    var _processArguments = function(mathEditor, args){


        var ret = {
            target : null,
            callback : null
        };

        _.each(args, function(arg){
            if(_.isFunction(arg)){
                ret.callback = arg;
            }else if(arg instanceof $){
                ret.target = arg;
            }
        });

        if(!ret.target){
            if(mathEditor.$target){
                ret.target = mathEditor.$target;
            }else{
                throw 'no target defined for rendering';
            }
        }

        return ret;
    };

    MathEditor.prototype.renderFromMathML = function(){

        var args = _processArguments(this, arguments);

        if(typeof(MathJax) !== 'undefined'){

            if(this.processing){
                return;
            }
            var jaxQueue = MathJax.Hub.queue;
            var mathStr = _wrapMathTags(this.mathML, (this.display === 'block'));
            this.$buffer.html(mathStr);

            var _this = this;
            jaxQueue.Push(
                ["Typeset", MathJax.Hub, this.$buffer[0]],
                function(){
                    _this.processing = false;

                    args.target.html(_this.$buffer.html());
                    _this.$buffer.empty();
                }
            );

            if(args.callback){
                jaxQueue.Push(args.callback);
            }
        }

    };

    MathEditor.prototype.renderFromTex = function(){

        var args = _processArguments(this, arguments);
        var _this = this;
        var jaxQueue = MathJax.Hub.queue;

        if(typeof (MathJax) !== 'undefined'){

            if(this.display === 'block'){
                _this.$buffer.text('\\[\\displaystyle{' + _this.tex + '}\\]');
            }else{
                _this.$buffer.text('\\(\\displaystyle{' + _this.tex + '}\\)');
            }

            //render preview:
            jaxQueue.Push(
                //programmatically typeset the buffer
                    ['Typeset', MathJax.Hub, _this.$buffer[0]],
                    function(){
                        var texJax;
                        try {
                            //replace the target element
                            args.target.html(_this.$buffer.html());

                            //store mathjax "tex", for tex for later mathML conversion
                            texJax = _getJaxByElement(_this.$buffer);

                            //empty buffer;
                            _this.$buffer.empty();

                            //sync MathML
                            if (typeof (texJax) !== 'undefined') {
                                _this.texToMathML(texJax, function (mathML) {
                                    _this.setMathML(_stripMathTags(mathML));
                                });
                            }
                        } catch (err) {
                            feedback().error('Mathjax error: ' + err.message);
                        }
                    }
                );

                if(args.callback){
                    jaxQueue.Push(args.callback);
                }
            }

        };

        var _stripMathTags = function(mathMLstr){

            mathMLstr = mathMLstr.replace(/<(\/)?math[^>]*>/g, '');
            mathMLstr = mathMLstr.replace(/^\s*[\r\n]/gm, '');//remove first blank line
            mathMLstr = mathMLstr.replace(/\s*[\r\n]$/gm, '');//last blank line

            return mathMLstr;
        };

        var _getJaxByElement = function($element){

            if($element instanceof $ && $element.length){
                var $script = $element.find('script');
                if($script.length && $script[0].MathJax && $script[0].MathJax.elementJax){
                    return $script[0].MathJax.elementJax;
                }
            }
        };

        var _wrapMathTags = function(mathMLstr, displayBlock){

            if(!mathMLstr.match(/<math[^>]*>/)){
                var display = displayBlock ? ' display="block"' : '';
                mathMLstr = '<math' + display + '>' + mathMLstr;//always show preview in block mode
            }
            if(!mathMLstr.match(/<\/math[^>]*>/)){
                mathMLstr += '</math>';
            }

            return mathMLstr;
        };

        MathEditor.prototype.texToMathML = function(texJax, callback){

            var _this = this;
            var mathML = '';

            try{
                mathML = texJax.root.toMathML('');
            }catch(err){
                if(!err.restart){
                    throw err;
                }
                return MathJax.Callback.After(function(){
                    _this.texToMathML(texJax, callback);
                }, err.restart);
            }

            MathJax.Callback(callback)(mathML);
        };

        return MathEditor;
    });
