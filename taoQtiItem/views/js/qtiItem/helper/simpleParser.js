define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiItem/helper/util',
    'taoQtiItem/qtiItem/core/Loader'
], function(_, $, util, Loader){
    "use strict";

    var _parsableElements = ['img', 'object'];
    var _qtiClassNames = {
        rubricblock : 'rubricBlock'
    };

    var _defaultOptions = {
        ns : {
            math : '',
            include : 'xi'
        },
        loaded : null,
        model : null
    };

    function _getElementSelector(qtiClass, ns){
        return ns ? ns + "\\:" + qtiClass + ','+qtiClass : qtiClass;
    }

    function getQtiClassFromXmlDom($node){

        var qtiClass = $node.prop('tagName').toLowerCase();

        //remove ns :
        qtiClass = qtiClass.replace(/.*:/, '');

        return _qtiClassNames[qtiClass] ? _qtiClassNames[qtiClass] : qtiClass;
    }

    function buildElement($elt){

        var qtiClass = getQtiClassFromXmlDom($elt);

        var elt = {
            qtiClass : qtiClass,
            serial : util.buildSerial(qtiClass + '_'),
            attributes : {}
        };

        $.each($elt[0].attributes, function(){
            if(this.specified){
                elt.attributes[this.name] = this.value;
            }
        });

        return elt;
    }

    function buildMath($elt, options){

        var elt = buildElement($elt);

        //set annotations:
        elt.annotations = {};
        $elt.find(_getElementSelector('annotation', options.ns.math)).each(function(){
            var $annotation = $(this);
            var encoding = $annotation.attr('encoding');
            if(encoding){
                elt.annotations[encoding] = _.unescape($annotation.html());
            }
            $annotation.remove();
        });

        //set math xml
        elt.mathML = $elt.html();

        //set ns:
        elt.ns = {
            name : 'm',
            uri : 'http://www.w3.org/1998/Math/MathML'//@todo : remove hardcoding there
        };

        return elt;
    }

    function parseContainer($container, opts){

        var options = _.merge(_.clone(_defaultOptions), opts || {});

        var ret = {
            serial : util.buildSerial('_container_'),
            body : '',
            elements : {}
        };

        _.each(_parsableElements, function(qtiClass){

            $container.find(qtiClass).each(function(){

                var $qtiElement = $(this);
                var element = buildElement($qtiElement, opts);

                ret.elements[element.serial] = element;
                $qtiElement.replaceWith(_placeholder(element));

            });

        });

        $container.find(_getElementSelector('math', options.ns.math)).each(function(){

            var $qtiElement = $(this);
            var element = buildMath($qtiElement, opts);

            ret.elements[element.serial] = element;
            $qtiElement.replaceWith(_placeholder(element));

        });

        $container.find(_getElementSelector('include', options.ns.include)).each(function(){

            var $qtiElement = $(this);
            var element = buildElement($qtiElement, opts);

            ret.elements[element.serial] = element;
            $qtiElement.replaceWith(_placeholder(element));

        });

        ret.body = $container.html();

        return ret;
    }

    function _placeholder(element){
        return '{{' + element.serial + '}}';
    }

    var parser = {
        parse : function(xmlStr, options){

            var $container = $(xmlStr);

            var element = buildElement($container, options);

            var data = parseContainer($container, options);

            if(data.body !== undefined){
                element.body = data;
            }

            if(_.isFunction(options.loaded) && options.model){
                var loader = new Loader().setClassesLocation(options.model);
                loader.loadAndBuildElement(element, options.loaded);
            }

            return element;
        }
    };

    return parser;
});

