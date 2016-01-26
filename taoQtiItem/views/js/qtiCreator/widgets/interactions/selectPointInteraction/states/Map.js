/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/SelectPointInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse', 
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/graphicInteractionShapeEditor',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/graphicScorePopup',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/response/graphicScoreMappingForm',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'ui/incrementer',
    'ui/tooltipster'
], function($, _, __, stateFactory, Map, commonRenderer, instructionMgr, graphicHelper, PciResponse, answerStateHelper, shapeEditor, grahicScorePopup, mappingFormTpl, formElement, incrementer, tooltipster){

    /**
     * Initialize the Map state.
     */
    function initMapState(){
        var widget          = this.widget;
        var interaction     = widget.element;
        var $container      = widget.$original; 
        var response        = interaction.getResponseDeclaration();

        widget._targets = [];

        if(!interaction.paper){
            return;
        }

        //really need to destroy before ? 
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);
        
        //add a specific instruction
        instructionMgr.appendInstruction(interaction, __('Please create areas that correspond to the response and associate them a score.\n' + 
                                                 'You can also position the target to the exact point as the correct response.'));
        interaction.responseMappingMode = true;
        if(_.isPlainObject(response.mapEntries)){
            response.mapEntries = _.values(response.mapEntries);
        }

        //here we do not use the common renderer but the creator's widget to get only a basic paper with the choices
        interaction.paper = widget.createPaper(); 
       
        //we create the shapes from the response mapping 
        setCurrentResponses(widget);
        
        //set up of the shape editor
        widget._editor = createEditor(widget);
     
        listenResponseAttrChange(widget);
  
    }

    /**
     * Creates an identifier based on an areaMapping entry
     * @private
     * @param {Object} area - the map entry
     * @returns {String} the identifier
     */
    function areaId(area){
        return area.shape + '-' + area.coords.replace(/,*/g, '-');
    }

    /**
     * Get the index of a area map entry in the entries
     * @private
     * @param {Object} response - the response declaration
     * @param {String} id - the identifier from areaId used to retrieve the index
     * @returns {Number|Boolean} the index or false
     */
    function getAreaIndex(response, id){
        var found = false;
        _.forEach(response.mapEntries, function(area, index){
            if(areaId(area) === id){
                found = index;
                return false;
            }
        });
        return found;
    }
 
     
    /**
     * Creates the shapes on the paper from the responses
     * @private
     * @param {Object} widget - the current widget
     */
    function setCurrentResponses(widget){
        var interaction = widget.element;
        var $container  = widget.$original; 
        var response    = interaction.getResponseDeclaration();
        var corrects    = _.values(response.getCorrect());

        //set up for the existing areas
        _.forEach(response.mapEntries, function(area){
            var shape = widget.createResponseArea(area.shape, area.coords);
            setUpScoringArea(widget, area, shape, false); 
        });

        //set up a target if
        if(answerStateHelper.defineCorrect(response) && corrects.length){
             _.forEach(corrects, function(correct){
                var point = correct.split(' ');
                if(point.length >= 2){
                   var target = graphicHelper.createTarget(interaction.paper, {
                        point : {
                            x : point[0],
                            y : point[1]
                        }
                   }); 
                   widget._targets.push(target.id);
                }
           });
        }

        //hide popups by clicking the paper 
        interaction.paper.getById('bg-image-' + interaction.serial).click(function(){
            $('.-mapping-editor', $container).hide();
        });
    }

    /**
     * Creates and initialize the shape editor 
     * @private
     * @param {Object} widget - the current widget
     * @return {ShapeEditor} the editor
     */
    function createEditor(widget){
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();

        //instantiate the shape editor, attach it to the widget to retrieve it during the exit phase
        var editor = shapeEditor(widget, {
            currents : response.mapEntries.map(areaId).concat(widget._targets),
            target : true,
            shapeCreated : function(shape, type){
                var point, corrects, area;
                if(type === 'target'){
                    //add a correct response 
                    point = shape.data('target');
                    corrects = response.getCorrect() || [];
                    corrects.push(qtiPoint(point));
                    response.setCorrect(corrects);

                } else {
                    //create an area for the mapping
                    area = {
                        shape  : type === 'path' ? 'poly' : type,
                        coords : graphicHelper.qtiCoords(shape),
                        mappedValue :  response.mappingAttributes.defaultValue || '0'
                    };
                    setUpScoringArea(widget, area, shape, true);

                    response.mapEntries.push(area);
                }
            },
            shapeRemoved : function(id, data){
                var scoreElt;
                if(/^target/.test(id) && data.target){
                    //remove from the correct response
                    response.setCorrect(
                        _.without(response.getCorrect(), qtiPoint(data.target))
                    );
                } else {
                    //remove the score and the popup
                    scoreElt = interaction.paper.getById('score-' + id);
                    if(scoreElt){
                        scoreElt.remove();
                    }
                    $('#score-popup-' + id).remove(); 
                    
                    //remove the area from the mapping
                    _.remove(response.mapEntries, function(area){
                        return id && areaId(area) === id;
                    });
                }
            },
            enterHandling : function(shape){
                if(shape.type === 'set'){
                    shape.forEach(function(setElt){
                        if(setElt.type === 'path'){
                            setElt.attr(graphicHelper._style['target-hover']);
                            return;
                        }
                    });
    
                } else {
                    //move the score back the shape and show the popup
                    shape.toFront();
                    $('#score-popup-' + shape.id).show(); 
                }
            },
            quitHandling : function(shape){
                if(shape.type === 'set'){
                    shape.forEach(function(setElt){
                        if(setElt.type === 'path'){
                            setElt.attr(graphicHelper._style['target-success']);
                            return;
                        }
                    });
                } else {
                    //move the score in above the shape and hide the popup
                    var scoreElt = interaction.paper.getById('score-' + shape.id);
                    if(scoreElt){
                        scoreElt.show().toFront();
                    }
                    $('#score-popup-' + shape.id).hide();
                } 
            },
            shapeChanging : function(shape){
                //move the score and the popup to create them again once the shape has moved
                var scoreElt = interaction.paper.getById('score-' + shape.id);
                if(scoreElt){
                    scoreElt.remove();
                }
                $('#score-popup-' + shape.id).remove(); 
            },
            shapeChange : function(shape){
                //now the shape has moved, so we update the mapping and create again the score and the popup
                var index = getAreaIndex(response, shape.id);
                var mapEntry;
                if(index !== false){
                    mapEntry = response.mapEntries[index];
                    response.mapEntries[index].coords = graphicHelper.qtiCoords(shape);
                    setUpScoringArea(widget, mapEntry, shape, mapEntry.mappedValue === response.mappingAttributes.defaultValue);
                    shape.id = areaId(mapEntry);
                }
            }
        });

        //Create our brand new editor
        editor.create();

        return editor;
    }

    /**
     * Format a point to the qti format
     * @param {Object} point
     * @param {Number} point.x 
     * @param {Number} point.y
     * @returns {String} the point as "x y"
     */
    function qtiPoint(point){
        return Math.round(point.x) + ' ' + Math.round(point.y);
    }
 
    /**
     * Creates the score element and the popup to view/edit the score 
     * @private
     * @param {Object} widget - the current widget
     * @param {Object} area   - the response area entry
     * @param {Raphael.Element} shape   - the related shape
     * @param {Boolean} default - if the score uses the default value
     */
    function setUpScoringArea(widget, area, shape, defaults){
        var interaction     = widget.element;
        var $container      = widget.$original; 
        var isResponsive    = $container.hasClass('responsive');
        var response        = interaction.getResponseDeclaration();
        var id              = areaId(area);        
        var $imageBox       = $('.main-image-box', $container);
        var $popup          = grahicScorePopup(interaction.paper, shape, $imageBox, isResponsive);
        var score           = area.mappedValue || response.mappingAttributes.defaultValue || '0';

        var scoreElt    = graphicHelper.createShapeText(interaction.paper, shape, {
                    id          : 'score-' + id,
                    content     : area.mappedValue + '',
                    style       : 'score-text-default',
                    title       : __('Score value'),
                    shapeClick  : true
                });
        scoreElt.data('default', !!defaults);
        shape.id = id; 

        $popup.attr('id', 'score-popup-' + id);

        //create manually the mapping form (detached)
        var $form = $(mappingFormTpl({
            score     : area.mappedValue,
            scoreMin  : response.getMappingAttribute('lowerBound'),
            scoreMax  : response.getMappingAttribute('upperBound'),
            noCorrect : true
        }));
        
        //set up the form data binding
        formElement.setChangeCallbacks($form, response, {
            score : function(response, value){
                if(value === ''){
                    scoreElt.attr({text : response.mappingAttributes.defaultValue})
                             .data('default', true);
                } else {
                    scoreElt.attr({text : value})
                             .data('default', false);
                }
                area.mappedValue = parseFloat(value);
            }
        });
        $form.appendTo($popup);
    }

    /**
     * Listen for changes in the response form that affects the creator : defaultValue and defineCorrect. 
     * @private
     * @param {Object} widget - the current widget
     */
    function listenResponseAttrChange(widget){
        var interaction = widget.element;
        var $container = widget.$container;        
        var $target = $container.find('[data-type="target"]');
        var $separator = $target.prev('.separator');

        //update the default scores when the form value change
        widget.on('mappingAttributeChange', function(data){
            if(data.key === 'defaultValue'){
                interaction.paper.forEach(function(element){
                    if(/^score/.test(element.id) && element.data('default') === true){
                        element.attr({text : data.value });
                    }
                });
            }
        });

        //update the targets when the defineCorrect field cahnges
        widget.on('metaChange', function(data){
            if(data.key === 'defineCorrect'){
               if(data.value){
                    $target.show();
                    $separator.show();
                } else {
                    $target.hide();
                    $separator.hide();
    
                    //remove targets
                    _.forEach(widget._targets, function(targetId){
                        var target = interaction.paper.getById(targetId);
                        var layer  = interaction.paper.getById('layer-' + targetId);
                        target.remove();
                        layer.remove();
                    }); 
                }
            } 
        });
    }

    /**
     * Exit the map state
     */
    function exitMapState(){
        var widget = this.widget;
        var interaction = widget.element;

        if(!interaction.paper){
            return;
        }

        if(widget._editor){
            widget._editor.destroy();
        }
        
        //destroy the common renderer
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction); 
        instructionMgr.removeInstructions(interaction);

        //initialize again the widget's paper
        interaction.paper = this.widget.createPaper();
    }

    /**
     * The map answer state for the selectPoint interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Map
     * @exports taoQtiItem/qtiCreator/widgets/interactions/selectPointInteraction/states/Map
     */
    return  stateFactory.create(Map, initMapState, exitMapState);
});
