define([
    'jquery',
    'lodash',
    'ui/ckeditor/dtdHandler',
    'taoQtiItem/qtiCreator/helper/qtiElements'
], function($, _, dtdHandler, qtiElements){
    'use strict';

    var _qtiHtmlEditableTypes = {
        'itemBody' : '.widget-textBlock > [data-html-editable]',
        'prompt' : '.qti-prompt[data-html-editable]',
        'choice' : '.qti-choice [data-html-editable]'
    };

    /**
     * Extends taoQtiItem/qtiCreator/editor/dtdHandler
     * to retrieve a collection of jQuery elements within a given context
     */
    var targetFinder = (function($, _, dh){

        /**
         * Inherit functions from parent class
         */
        var inheritedFunctions = (function(){
            var fns = {}, fn;
            for(fn in dh){
                if(!dh.hasOwnProperty(fn)){
                    continue;
                }
                if(!(dh[fn] instanceof Function)){
                    continue;
                }
                fns[fn] = dh[fn];
            }
            return fns;
        }());

        /**
         * Find potential targets within a given context
         *
         * @param child
         * @param context (string|DOM element|jQuery element), defaults to document.body
         * @returns {*}
         */
        var getTargetsFor = function(qtiClass, context){

            var child,
                $qtiContainers,
                $targets = $();

            context = context || document.body;
            context = $(context);

            if(qtiElements.is(qtiClass, 'inlineInteraction')){

                $qtiContainers = context.find(_qtiHtmlEditableTypes['itemBody']);
                child = 'object';

            }else{

                $qtiContainers = context.find(_.values(_qtiHtmlEditableTypes).join(','));
                $qtiContainers = context.find(_qtiHtmlEditableTypes['itemBody']);//beta limitation
                switch(qtiClass){
                    case 'math':
                        child = 'object';
                        break;
                    case 'object.audio':
                    case 'object.video':
                        child = 'object';
                        break;
                    default:
                        child = qtiClass;
                }
            }

            var parents = dh.getParentsOf(child),
                parentSelector = parents.join(',');

            $qtiContainers.each(function(){

                var $qtiContainer = $(this),
                    $widget = $qtiContainer.closest('.widget-box');

                if($qtiContainer.is(parentSelector)){
                    $targets = $targets.add($qtiContainer);
                }
                $targets = $targets.add($qtiContainer.find(parentSelector).not(function(){
                    var $closest = $(this).closest('.widget-box');
                    return ($closest.length && $closest[0] !== $widget[0]);
                }));
            });

            return $targets;
        };

        /**
         * return both parent and own functions
         */
        return _.extend(inheritedFunctions, {
            getTargetsFor : getTargetsFor
        });

    }($, _, dtdHandler));

    return targetFinder;
});

