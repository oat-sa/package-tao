/**
 * @author Sam <sams@taotesting.com>
 * @requires jquery
 * @requires lodash
 */
define(['jquery', 'lodash'], function($, _){

    var QtiElements = {};

    QtiElements.classes = {
        //abstract classes:
        'itemBody' : {'parents' : ['bodyElement'], 'contains' : ['block'], 'abstract' : true},
        'atomicBlock' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['inline'], 'abstract' : true},
        'atomicInline' : {'parents' : ['bodyElement', 'flowStatic', 'inlineStatic'], 'contains' : ['inline'], 'abstract' : true},
        'simpleBlock' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['block'], 'abstract' : true},
        'simpleInline' : {'parents' : ['bodyElement', 'flowStatic', 'inlineStatic'], 'contains' : ['inline'], 'abstract' : true},
        'flowStatic' : {'parents' : ['flow'], 'abstract' : true},
        'blockStatic' : {'parents' : ['block'], 'abstract' : true},
        'inlineStatic' : {'parents' : ['inline'], 'abstract' : true},
        'flow' : {'parents' : ['objectFlow'], 'abstract' : true},
        'tableCell' : {'parents' : ['bodyElement'], 'contains' : ['flow'], 'abstract' : true},
        //html-derived qti elements:
        'caption' : {'parents' : ['bodyElement'], 'contains' : ['inline'], 'xhtml' : true},
        'col' : {'parents' : ['bodyElement'], 'xhtml' : true},
        'colgroup' : {'parents' : ['bodyElement'], 'contains' : ['col'], 'xhtml' : true},
        'div' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['flow'], 'xhtml' : true},
        'dl' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['dlElement'], 'xhtml' : true},
        'dt' : {'parents' : ['dlElement'], 'contains' : ['inline'], 'xhtml' : true},
        'dd' : {'parents' : ['dlElement'], 'contains' : ['flow'], 'xhtml' : true},
        'hr' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'xhtml' : true},
        'math' : {'parents' : ['blockStatic', 'flowStatic', 'inlineStatic'], 'xhtml' : true},
        'li' : {'parents' : ['bodyElement'], 'contains' : ['flow'], 'xhtml' : true},
        'ol' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['li'], 'xhtml' : true},
        'ul' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['li'], 'xhtml' : true},
        'object' : {'parents' : ['bodyElement', 'flowStatic', 'inlineStatic'], 'contains' : ['objectFlow'], 'xhtml' : true},
        'param' : {'parents' : ['objectFlow'], 'xhtml' : true},
        'table' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'contains' : ['caption', 'col', 'colgroup', 'thead', 'tfoot', 'tbody'], 'xhtml' : true},
        'tbody' : {'parents' : ['bodyElement'], 'contains' : ['tr'], 'xhtml' : true},
        'tfoot' : {'parents' : ['bodyElement'], 'contains' : ['tr'], 'xhtml' : true},
        'thead' : {'parents' : ['bodyElement'], 'contains' : ['tr'], 'xhtml' : true},
        'td' : {'parents' : ['tableCell'], 'xhtml' : true},
        'th' : {'parents' : ['tableCell'], 'xhtml' : true},
        'tr' : {'parents' : ['bodyElement'], 'contains' : ['tableCell'], 'xhtml' : true},
        'a' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'abbr' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'acronym' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'b' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'big' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'cite' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'code' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'dfn' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'em' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'i' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'kbd' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'q' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'samp' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'small' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'span' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'strong' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'sub' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'sup' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'tt' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'var' : {'parents' : ['simpleInline'], 'xhtml' : true},
        'blockquote' : {'parents' : ['simpleBlock'], 'xhtml' : true},
        'address' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'h1' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'h2' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'h3' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'h4' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'h5' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'h6' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'p' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'pre' : {'parents' : ['atomicBlock'], 'xhtml' : true},
        'img' : {'parents' : ['atomicInline'], 'xhtml' : true, attributes : ['src', 'alt', 'longdesc', 'height', 'width']},
        'br' : {'parents' : ['atomicInline'], 'xhtml' : true},
        //qti elements:
        'infoControl' : {'parents' : ['blockStatic', 'bodyElement', 'flowStatic'], 'qti' : true},
        'textRun' : {'parents' : ['inlineStatic', 'flowstatic'], 'qti' : true},
        'feedbackInline' : {'parents' : ['simpleInline', 'feedbackElement'], 'qti' : true},
        'feedbackBlock' : {'parents' : ['simpleBlock'], 'qti' : true},
        'rubricBlock' : {'parents' : ['simpleBlock'], 'qti' : true}, //strange qti 2.1 exception, marked as simpleBlock instead of
        'blockInteraction' : {'parents' : ['block', 'flow', 'interaction'], 'qti' : true},
        'inlineInteraction' : {'parents' : ['inline', 'flow', 'interaction'], 'qti' : true},
        'gap' : {'parents' : ['inlineStatic'], 'qti' : true},
        'hottext' : {'parents' : ['flowstatic', 'inlineStatic'], 'contains' : ['inlineStatic'], 'qti' : true},
        'printedVariable' : {'parents' : ['bodyElement', 'flowStatic', 'inlineStatic', 'textOrVariable'], 'qti' : true},
        'prompt' : {'parents' : ['bodyElement'], 'contains' : ['inlineStatic'], 'qti' : true},
        'templateElement' : {'parents' : ['bodyElement'], 'qti' : true},
        'templateBlock' : {'parents' : ['blockStatic', 'flowStatic', 'templateElement'], 'contains' : ['blockStatic'], 'qti' : true},
        'templateInline' : {'parents' : ['inlineStatic', 'flowStatic', 'templateElement'], 'contains' : ['inlineStatic'], 'qti' : true},
        'choiceInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'associateInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'orderInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'matchInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'hottextInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'gapMatchInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'mediaInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'sliderInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'uploadInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'drawingInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'graphicInteraction' : {'parents' : ['blockInteraction'], 'qti' : true},
        'hotspotInteraction' : {'parents' : ['graphicInteraction'], 'qti' : true},
        'graphicAssociateInteraction' : {'parents' : ['graphicInteraction'], 'qti' : true},
        'graphicOrderInteraction' : {'parents' : ['graphicInteraction'], 'qti' : true},
        'graphicGapMatchInteraction' : {'parents' : ['graphicInteraction'], 'qti' : true},
        'selectPointInteraction' : {'parents' : ['graphicInteraction'], 'qti' : true},
        'textEntryInteraction' : {'parents' : ['stringInteraction', 'inlineInteraction'], 'qti' : true},
        'extendedTextInteraction' : {'parents' : ['stringInteraction', 'blockInteraction'], 'qti' : true},
        'inlineChoiceInteraction' : {'parents' : ['inlineInteraction'], 'qti' : true},
        '_container' : {'parents' : ['block'], 'qti' : true}//a pseudo class introduced in TAO
    };

    QtiElements.cache = {containable : {}, children : {}, parents : {}};

    QtiElements.getAllowedContainersElements = function(qtiClass, $container){
        var classes = QtiElements.getAllowedContainers(qtiClass);
        var jqSelector = '';
        for(var i in classes){
            if(classes[i].qti){
                //qti element:

            }else{
                //html element:
                jqSelector += classes[i] + ', ';
            }
        }

        if(jqSelector){
            jqSelector = jqSelector.substring(0, jqSelector.length - 2);
        }

        return $(jqSelector, $container ? $container : $(document)).filter(':not([data-qti-type])');
    };

    QtiElements.getAllowedContainers = function(qtiClass){

        if(QtiElements.cache.containable[qtiClass]){
            var ret = QtiElements.cache.containable[qtiClass];
        }else{
            var ret = [];
            var parents = QtiElements.getParentClasses(qtiClass, true);
            for(var aClass in QtiElements.classes){
                var model = QtiElements.classes[aClass];
                if(model.contains){
                    var intersect = _.intersection(model.contains, parents);
                    if(intersect.length){
                        if(!model.abstract){
                            ret.push(aClass);
                        }
                        ret = _.union(ret, QtiElements.getChildClasses(aClass, true));
                    }
                }
            }
            QtiElements.cache.containable[qtiClass] = ret;
        }

        return ret;
    };

    QtiElements.getAllowedContents = function(qtiClass, recursive, checked){

        var ret = [];
        checked = checked || {};

        var model = QtiElements.classes[qtiClass];
        if(model && model.contains){
            for(var i in model.contains){
                var contain = model.contains[i];
                if(!checked[contain]){
                    checked[contain] = true;

                    //qtiClass can contain everything defined as its contents
                    ret.push(contain);

                    //qtiClass can also contain subclass of its contents
                    var children = QtiElements.getChildClasses(contain, true);
                    for(var i in children){
                        var child = children[i];
                        if(!checked[child]){
                            checked[child] = true;

                            ret.push(child);

                            //adding children allowed contents depends on the option "recursive"
                            if(recursive){
                                ret = _.union(ret, QtiElements.getAllowedContents(child, true, checked));
                            }
                        }
                    }

                    //adding allowed contents of qtiClass' allowed contents depends on the option "recursive"
                    if(recursive){
                        ret = _.union(ret, QtiElements.getAllowedContents(contain, true, checked));
                    }

                }
            }
        }

        //qtiClass can contain all allowed contents of its parents:
        var parents = QtiElements.getParentClasses(qtiClass, true);
        for(var i in parents){
            ret = _.union(ret, QtiElements.getAllowedContents(parents[i], recursive, checked));
        }

        return _.uniq(ret, false);
    };

    QtiElements.isAllowedClass = function(qtiContainerClass, qtiContentClass){
        var allowedClasses = QtiElements.getAllowedContents(qtiContainerClass);
        return (_.indexOf(allowedClasses, qtiContentClass) >= 0);
    };

    QtiElements.getParentClasses = function(qtiClass, recursive){

        if(recursive && QtiElements.cache.parents[qtiClass]){
            var ret = QtiElements.cache.parents[qtiClass];
        }else{
            var ret = [];
            if(QtiElements.classes[qtiClass]){
                ret = QtiElements.classes[qtiClass].parents;
                if(recursive){
                    for(var i in ret){
                        ret = _.union(ret, QtiElements.getParentClasses(ret[i], recursive));
                    }
                    ret = _.uniq(ret, false);
                }
            }
            QtiElements.cache.parents[qtiClass] = ret;
        }

        return ret;
    };

    QtiElements.getChildClasses = function(qtiClass, recursive, type){

        var cacheType = type ? type : 'all';
        if(recursive && QtiElements.cache.children[qtiClass] && QtiElements.cache.children[qtiClass][cacheType]){
            var ret = QtiElements.cache.children[qtiClass][cacheType];
        }else{
            var ret = [];
            for(var aClass in QtiElements.classes){
                var model = QtiElements.classes[aClass];
                if(_.indexOf(model.parents, qtiClass) >= 0){
                    if(type){
                        if(model[type]){
                            ret.push(aClass);
                        }
                    }else{
                        ret.push(aClass);
                    }
                    if(recursive){
                        ret = _.union(ret, QtiElements.getChildClasses(aClass, recursive, type));
                    }
                }
            }
            if(!QtiElements.cache.children[qtiClass]){
                QtiElements.cache.children[qtiClass] = {};
            }
            QtiElements.cache.children[qtiClass][cacheType] = ret;
        }

        return ret;
    };

    QtiElements.isBlock = function(qtiClass){
        return QtiElements.is(qtiClass, 'block');
    };

    QtiElements.isInline = function(qtiClass){
        return QtiElements.is(qtiClass, 'inline');
    };

    QtiElements.is = function(qtiClass, topClass){
        if(qtiClass === topClass){
            return true;
        }else{
            var parents = QtiElements.getParentClasses(qtiClass, true);
            return (_.indexOf(parents, topClass) >= 0);
        }
    };
    
    QtiElements.getAvailableAuthoringElements = function(){
        
        return {
            choiceInteraction : {
                title : __('Choice Interaction'),
                icon : 'choice',
                short : __('Choice'),
                qtiClass : 'choiceInteraction',
                tags:['Common Interaction', 'mcq']
            },
            orderInteraction : {
                title : __('Order Interaction'),
                icon : 'order',
                short : __('Order'),
                qtiClass : 'orderInteraction',
                tags:['Common Interaction', 'ordering']
            },
            associateInteraction : {
                title : __('Associate Interaction'),
                icon : 'associate',
                short : __('Associate'),
                qtiClass : 'associateInteraction',
                tags:['Common Interaction', 'association']
            },
            matchInteraction : {
                title : __('Match Interaction'),
                icon : 'match',
                short : __('Match'),
                qtiClass : 'matchInteraction',
                tags:['Common Interaction', 'association']
            },
            hottextInteraction : {
                title : __('Hottext Interaction'),
                icon : 'hottext',
                short : __('Hottext'),
                qtiClass : 'hottextInteraction',
                tags:['Common Interaction', 'text']
            },
            gapMatchInteraction : {
                title : __('Gap Match Interaction'),
                icon : 'gap-match',
                short : __('Gap Match'),
                qtiClass : 'gapMatchInteraction',
                tags:['Common Interaction', 'text', 'association']
            },
            sliderInteraction : {
                title : __('Slider Interaction'),
                icon : 'slider',
                short : __('Slider'),
                qtiClass : 'sliderInteraction',
                tags:['Common Interaction', 'special']
            },
            extendedTextInteraction : {
                title : __('Extended Text Interaction'),
                icon : 'extended-text',
                short : __('Extended Text'),
                qtiClass : 'extendedTextInteraction',
                tags:['Common Interaction', 'text']
            },
            uploadInteraction : {
                title : __('File Upload Interaction'),
                icon : 'upload',
                short : __('File Upload'),
                qtiClass : 'uploadInteraction',
                tags:['Common Interaction', 'special']
            },
            mediaInteraction : {
                title : __('Media Interaction'),
                icon : 'media',
                short : __('Media'),
                qtiClass : 'mediaInteraction',
                tags:['Common Interaction', 'media']
            },
            _container : {
                title : __('Text Block Interaction'),
                icon : 'font',
                short : __('Block'),
                qtiClass : '_container',
                tags:['Inline Interactions', 'text']
            },
            inlineChoiceInteraction : {
                title : __('Inline Choice Interaction'),
                icon : 'inline-choice',
                short : __('Inline Choice'),
                qtiClass : 'inlineChoiceInteraction',
                tags:['Inline Interactions', 'inline-interactions', 'mcq']
            },
            textEntryInteraction : {
                title : __('Text Entry Interaction'),
                icon : 'text-entry',
                short : __('Text Entry'),
                qtiClass : 'textEntryInteraction',
                tags:['Inline Interactions', 'inline-interactions', 'text']
            },
            hotspotInteraction : {
                title : __('Hotspot Interaction'),
                icon : 'hotspot',
                short : __('Hotspot'),
                qtiClass : 'hotspotInteraction',
                tags:['Graphic Interactions', 'mcq']
            },
            graphicOrderInteraction : {
                title : __('Graphic Order Interaction'),
                icon : 'graphic-order',
                short : __('Graphic Order'),
                qtiClass : 'graphicOrderInteraction',
                tags:['Graphic Interactions', 'ordering']
            },
            graphicAssociateInteraction : {
                title : __('Graphic Associate Interaction'),
                icon : 'graphic-associate',
                short : __('Graphic Associate'),
                qtiClass : 'graphicAssociateInteraction',
                tags:['Graphic Interactions', 'association']
            },
            graphicGapMatchInteraction : {
                title : __('Choice Interaction'),
                icon : 'graphic-gap',
                short : __('Graphic Gap'),
                qtiClass : 'graphicGapMatchInteraction',
                tags:['Graphic Interactions', 'association']
            },
            selectPointInteraction : {
                title : __('Select Point Interaction'),
                icon : 'select-point',
                short : __('Select Point'),
                qtiClass : 'selectPointInteraction',
                tags:['Graphic Interactions']
            }
        };
    };
    
    return QtiElements;

});