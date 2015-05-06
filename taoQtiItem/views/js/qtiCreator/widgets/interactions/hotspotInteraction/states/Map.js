/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/HotspotInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse', 
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/graphicScorePopup',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/response/graphicScoreMappingForm',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'ui/deleter',
    'ui/tooltipster'
], function($, _, __, stateFactory, Map, HotspotInteraction, helper, graphicHelper, PciResponse, answerStateHelper, grahicScorePopup, mappingFormTpl, formElement, deleter, tooltipster){

    /**
     * Initialize the state.
     */
    function initMapState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();

        //really need to destroy before ? 
        HotspotInteraction.destroy(interaction);
        
        if(!interaction.paper){
            return;
        }

        //add a specific instruction
        helper.appendInstruction(interaction, __('Please the score of each hotspot choice.'));
        interaction.responseMappingMode = true;

        //here we do not use the common renderer but the creator's widget to get only a basic paper with the choices
        interaction.paper = widget.createPaper();     
        widget.createChoices(); 

        initResponseMapping(widget);           

        //set the current corrects responses on the paper
        HotspotInteraction.setResponse(interaction, PciResponse.serialize(_.values(response.getCorrect()), interaction));   
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

        //destroy the common renderer
        helper.removeInstructions(interaction);
        HotspotInteraction.destroy(interaction); 

        //initialize again the widget's paper
        interaction.paper = widget.createPaper();
        widget.createChoices(); 
    }


    /**
     * Set up all elements to set the response mapping.
     * TODO this method needs to be split
     * @param {Oject} widget - the current widget
     */
    function initResponseMapping(widget){
        var scoreTexts      = {};
        var interaction     = widget.element;
        var $container      = widget.$original; 
        var isResponsive    = $container.hasClass('responsive');
        var $imageBox       = $('.main-image-box', $container);
        var response        = interaction.getResponseDeclaration();
        var mapEntries      = response.getMapEntries(); 
        var corrects        = _.values(response.getCorrect());

        //get the shape of each choice
        _.forEach(interaction.getChoices(), function(choice){    
            var shape = interaction.paper.getById(choice.serial);
            var $popup = grahicScorePopup(interaction.paper, shape, $imageBox, isResponsive);
            var score = mapEntries[choice.id()] || response.mappingAttributes.defaultValue || '0'; 

            //create an SVG  text from the default mapping value
            scoreTexts[choice.serial] = graphicHelper.createShapeText(interaction.paper, shape, {
                id          : 'score-' + choice.serial,
                content     : score,
                style       : 'score-text-default',
                shapeClick  : true
            }).data('default', true); 
          
            //create manually the mapping form (detached)
            var $form = $(mappingFormTpl({
                identifier          : choice.id(),
                correctDefined      : answerStateHelper.isCorrectDefined(widget),
                correct             : _.contains(response.getCorrect(), choice.id()),
                score               : score,
                scoreMin            : response.getMappingAttribute('lowerBound'),
                scoreMax            : response.getMappingAttribute('upperBound')
            }));

            //set up the form data binding
            formElement.setChangeCallbacks($form, response, {
                score : function(response, value){
                    var scoreText = scoreTexts[choice.serial];
                    if(value === ''){
                        response.removeMapEntry(choice.id());
                        scoreText.attr({text : response.mappingAttributes.defaultValue})
                                 .data('default', true);
                    } else {
                        response.setMapEntry(choice.id(), value, true);
                        scoreText.attr({text : value})
                                 .data('default', false);
                    }
                }, 
                correct : function(response, value){
                    if(value === true){
                        if(!_.contains(corrects, choice.id())){
                            corrects.push(choice.id());
                            shape.active = true;
                            graphicHelper.updateElementState(shape, 'active');
                        }
                    } else {
                        corrects = _.without(corrects, choice.id());
                        shape.active = false;
                        graphicHelper.updateElementState(shape, 'basic');
                    }
                    response.setCorrect(corrects);
                }
            });

            shape.click(function(){
                $('.mapping-editor', $container).hide();
                $popup.show();
            });

            $popup.append($form);
        });

        //set up ui components used by the form
        deleter($container);
        tooltipster($container);
        
        interaction.paper.getById('bg-image-' + interaction.serial).click(function(){
            $('.mapping-editor', $container).hide();
        });
        
        //the click on the cross hides the popup
        $('.mapping-editor', $container).on('click', '.closer', function(){
            $(this).parent('.mapping-editor').hide();
        });

        //update the elements on attribute changes
        widget.on('mappingAttributeChange', function(data){
            if(data.key === 'defaultValue'){
                _.forEach(scoreTexts, function(scoreText){
                    if(scoreText.data('default') === true){
                        scoreText.attr({text : data.value });
                    }
                });
            }
        });
    }


    /**
     * The map answer state for the hotspot interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Map
     * @exports taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/Map
     */
    return  stateFactory.create(Map, initMapState, exitMapState);
});
