/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GapMatchInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse', 
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/pairScoringForm' 
], function($, _, __, stateFactory, Map, commonRenderer, instructionMgr, PciResponse, scoringFormFactory){

    /**
     * Initialize the state.
     */
    function initMapState(){
        var widget = this.widget;
        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var corrects  = _.values(response.getCorrect());
        var currentResponses =  _.size(response.getMapEntries()) === 0 ? corrects : _.keys(response.getMapEntries());

        
        //really need to destroy before ? 
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);
        
        //add a specific instruction
        instructionMgr.appendInstruction(interaction, __('Please fill the gap with the texts below, then edit the score for each pair.'));
        
        //set the current mapping mode, needed by the common renderer
        interaction.responseMappingMode = true;
 
        //use the common Renderer
        commonRenderer.render.call(interaction.getRenderer(), interaction);
    
        //change the display of the gaps
        displayGaps(widget.$container);

        //and initialize the scoring form
        if(_.size(response.getMapEntries()) === 0){
            updateForm(widget, corrects);
        } else {
            updateForm(widget);
        }
        
        //each response change leads to an update of the scoring form
        widget.$container.on('responseChange.qti-widget', function(e, data){
            var type  = response.attr('cardinality') === 'single' ? 'base' : 'list';
            var pairs, entries;
            if(data && data.response &&  data.response[type]){
               pairs = _.invoke(data.response[type].directedPair, Array.prototype.join, ' ');
               entries = _.keys(response.getMapEntries());
                
               //add new pairs from  the difference between the current entries and the given data
               _(pairs).difference(entries).forEach(interaction.pairScoringForm.addPair, interaction.pairScoringForm);
            }

            displayGaps(widget.$container);
        });
    }

    /**
     * Exit the map state
     */
    function exitMapState(){
        var widget = this.widget;
        var interaction = widget.element;
        
        widget.$container.off('responseChange.qti-widget');

        if(interaction.pairScoringForm){
            interaction.pairScoringForm.destroy();
        }

        //destroy the common renderer
        commonRenderer.resetResponse(interaction); 
        commonRenderer.destroy(interaction);

        instructionMgr.removeInstructions(interaction);
    }

    /**
     * The gap in this states show their identifier, to visually identify them.
     * @param {jQueryElement} $container - the interaction container
     */
    function displayGaps($container){
        $('.gapmatch-content', $container)
          .removeClass('filled')            //this disable gap selection
          .addClass('gap-info')
          .each(function(){
            var $elt = $(this);
            $elt.text($elt.data('identifier'));
        });
    }

    /**
     * Update the scoring form
     * @param {Object} widget - the current widget
     * @param {Array} [entries] - to force the use of this collection instead of the mapEntries
     */
    function updateForm(widget, entries){

        var interaction = widget.element;
        var response = interaction.getResponseDeclaration();
        var mapEntries = response.getMapEntries();

        //keep a map of choices descriptions for the formatLeft callback
        var formatedChoices = _.transform(interaction.getChoices(), function(acc, choice){
            acc[choice.id()] = choice.val();
        }, {});

        var mappingChange = function mappingChange(){
            //set the current responses, either the mapEntries or the corrects if nothing else
            commonRenderer.setResponse(
                interaction, 
                PciResponse.serialize(_.invoke(_.keys(response.getMapEntries()), String.prototype.split, ' '), interaction)
            );
        };

        //set up the scoring form options
        var options = {
            leftTitle : __('Choice'),
            rightTitle : __('Gap'),
            type : 'directedPair',
            pairLeft : function(){
                return _.map(interaction.getChoices(), function(choice){
                    return {
                        id : choice.id(),
                        value : choice.val()
                    };
                });
            },
            pairRight : function(){
                return _.map(interaction.getGaps(), function(gap){
                    return {
                        id : gap.id(),
                        value : gap.id()
                    };
                });
            },
            formatLeft : function(value){
                return formatedChoices[value] || value;
            }
        };

        //format the entries to match the needs of the scoring form
        if(entries){
            options.entries = _.transform(entries, function(result, value){
                result[value] = mapEntries[value] !== undefined ? mapEntries[value] : response.mappingAttributes.defaultValue;
            }, {}); 
        }

        //initialize the scoring form 
        interaction.pairScoringForm = scoringFormFactory(widget, options);
    }

    /**
     * The map answer state for the graphicGapMatch interaction
     * @extends taoQtiItem/qtiCreator/widgets/states/Map
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/Map
     */
    return  stateFactory.create(Map, initMapState, exitMapState);
});
