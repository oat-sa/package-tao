define([
    'lodash',
    'i18n',
    'jquery',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/associateInteraction',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/associateInteraction.pair',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'taoQtiItem/qtiCreator/helper/adaptSize',
    'eyecatcher'
], function(_, __, $, tpl, pairTpl, Helper, pciResponse, adaptSize, eyecatcher){

    /**
     * Global variable to count number of choice usages:
     * @type type
     */
    var _choiceUsages = {};

    var setChoice = function(interaction, $choice, $target){

        var choiceSerial = $choice.data('serial'),
            choice = interaction.getChoice(choiceSerial);

        if(!choiceSerial){
            throw 'empty choice serial';
        }

        if(!_choiceUsages[choiceSerial]){
            _choiceUsages[choiceSerial] = 0;
        }
        _choiceUsages[choiceSerial]++;

        var _setChoice = function(){

            $target
                .data('serial', choiceSerial)
                .html($choice.html())
                .addClass('filled');

            if(!interaction.responseMappingMode &&
                choice.attr('matchMax') &&
                _choiceUsages[choiceSerial] >= choice.attr('matchMax')){

                $choice.addClass('deactivated');
            }


        };

        if($target.siblings('div').hasClass('filled')){

            var $resultArea = Helper.getContainer(interaction).find('.result-area'),
                $pair = $target.parent(),
                thisPairSerial = [$target.siblings('div').data('serial'), choiceSerial],
                $otherRepeatedPair = $();

            //check if it is not a repeating association!
            $resultArea.children().not($pair).each(function(){
                var $otherPair = $(this).children('.filled');
                if($otherPair.length === 2){
                    var otherPairSerial = [$($otherPair[0]).data('serial'), $($otherPair[1]).data('serial')];
                    if(_.intersection(thisPairSerial, otherPairSerial).length === 2){
                        $otherRepeatedPair = $otherPair;
                        return false;
                    }
                }
            });

            if($otherRepeatedPair.length === 0){
                //no repeated pair, so allow the choice to be set:
                _setChoice();

                //trigger pair made event
                Helper.triggerResponseChangeEvent(interaction, {
                    type : 'added',
                    $pair : $pair,
                    choices : thisPairSerial
                });

                Helper.validateInstructions(interaction, {choice : $choice, target : $target});

                if(interaction.responseMappingMode || parseInt(interaction.attr('maxAssociations')) === 0){

                    $pair.removeClass('incomplete-pair');

                    //append new pair option?
                    if(!$resultArea.children('.incomplete-pair').length){
                        $resultArea.append(pairTpl({empty : true}));
                        $resultArea.children('.incomplete-pair').fadeIn(600, function(){
                            $(this).show();
                        });
                    }
                }
            }else{
                //repeating pair: show it:

                //@todo add a notification message here in warning
                $otherRepeatedPair.css('border', '1px solid orange');
                $target.html(__('identical pair already exists')).css({
                    color : 'orange',
                    border : '1px solid orange'
                });
                setTimeout(function(){
                    $otherRepeatedPair.removeAttr('style');
                    $target.empty().removeAttr('style');
                }, 2000);
            }

        }else{
            _setChoice();
        }
    };

    var unsetChoice = function(interaction, $choice, animate){

        var serial = $choice.data('serial'),
            $container = Helper.getContainer(interaction);

        $container.find('.choice-area [data-serial=' + serial + ']').removeClass('deactivated');

        _choiceUsages[serial]--;

        $choice
            .removeClass('filled')
            .removeData('serial')
            .empty();

        if(!interaction.swapping){

            //a pair with one single element is not valid, so consider the response to be modified:
            Helper.triggerResponseChangeEvent(interaction, {
                type : 'removed',
                $pair : $choice.parent()
            });
            Helper.validateInstructions(interaction, {choice : $choice});

            //completely empty pair: 
            if(!$choice.siblings('div').hasClass('filled') && (parseInt(interaction.attr('maxAssociations')) === 0 || interaction.responseMappingMode)){
                //shall we remove it?
                var $parent = $choice.parent();
                if(!$parent.hasClass('incomplete-pair')){
                    if(animate){
                        $parent.addClass('removing').fadeOut(500, function(){
                            $(this).remove();
                        });
                    }else{
                        $parent.remove();
                    }
                }
            }
        }
    };

    var getChoice = function(interaction, identifier){

        //warning: do not use selector data-identifier=identifier because data-identifier may change dynamically
        var choice = interaction.getChoiceByIdentifier(identifier);
        if(!choice){
            throw new Error('cannot find a choice with the identifier : ' + identifier);
        }
        return Helper.getContainer(interaction).find('.choice-area [data-serial=' + choice.getSerial() + ']');
    };

    var renderEmptyPairs = function(interaction){

        var max = parseInt(interaction.attr('maxAssociations')),
            $resultArea = Helper.getContainer(interaction).find('.result-area');

        if(interaction.responseMappingMode || max === 0){
            $resultArea.append(pairTpl({empty : true}));
            $resultArea.children('.incomplete-pair').show();
        }else{
            for(var i = 0; i < max; i++){
                $resultArea.append(pairTpl());
            }
        }
    };

    var _adaptSize = function(interaction){
        _.delay(function(){
            adaptSize.height(Helper.getContainer(interaction).find('.result-area .target, .choice-area .qti-choice'));
        }, 200);//@todo : fix the image loading issues
    };
    
    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10291
     * 
     * @param {object} interaction
     */
    var render = function(interaction){

        renderEmptyPairs(interaction);

        var $container = Helper.getContainer(interaction),
            $choiceArea = $container.find('.choice-area'),
            $resultArea = $container.find('.result-area'),
            $activeChoice = null;


        var _getChoice = function(serial){
            return $choiceArea.find('[data-serial=' + serial + ']');
        };

        /**
         * @todo Tried to store $resultArea.find[...] in a variable but this fails
         * @param $choice
         * @param $target
         * @private
         */
        var _setChoice = function($choice, $target){
            setChoice(interaction, $choice, $target);
            _adaptSize(interaction);
        };

        var _resetSelection = function(){
            if($activeChoice){
                $resultArea.find('.remove-choice').remove();
                $activeChoice.removeClass('active');
                $container.find('.empty').removeClass('empty');
                $activeChoice = null;
            }
        };

        var _unsetChoice = function($choice){
            unsetChoice(interaction, $choice, true);
            _adaptSize(interaction);
        };

        var _isInsertionMode = function(){
            return ($activeChoice && $activeChoice.data('identifier'));
        };

        var _isModeEditing = function(){
            return ($activeChoice && !$activeChoice.data('identifier'));
        };

        $container.on('mousedown.commonRenderer', function(e){
            _resetSelection();
        });

        $choiceArea.on('mousedown.commonRenderer', '>li', function(e){

            e.stopPropagation();

            if($(this).hasClass('deactivated')){
                e.preventDefault();
                return;
            }

            if(_isModeEditing()){
                //swapping:
                interaction.swapping = true;
                _unsetChoice($activeChoice);
                _setChoice($(this), $activeChoice);
                _resetSelection();
                interaction.swapping = false;
            }else{

                if($(this).hasClass('active')){
                    _resetSelection();
                }else{
                    _resetSelection();

                    //activate it:
                    $activeChoice = $(this);
                    $(this).addClass('active');
                    $resultArea.find('>li>.target').addClass('empty');
                }
            }

        });

        $resultArea.on('mousedown.commonRenderer', '>li>div', function(e){

            e.stopPropagation();

            if(_isInsertionMode()){

                var $target = $(this),
                    choiceSerial = $activeChoice.data('serial'),
                    targetSerial = $target.data('serial');

                if(targetSerial !== choiceSerial){

                    if($target.hasClass('filled')){
                        interaction.swapping = true;//hack to prevent deleting empty pair in infinite association mode
                    }

                    //set choices:
                    if(targetSerial){
                        _unsetChoice($target);
                    }

                    _setChoice($activeChoice, $target);

                    //always reset swapping mode after the choice is set
                    interaction.swapping = false;
                }

                _resetSelection();

            }else if(_isModeEditing()){

                //editing mode:
                var $target = $(this),
                    targetSerial = $target.data('serial'),
                    choiceSerial = $activeChoice.data('serial');

                if(targetSerial !== choiceSerial){

                    if($target.hasClass('filled') || $activeChoice.siblings('div')[0] === $target[0]){
                        interaction.swapping = true;//hack to prevent deleting empty pair in infinite association mode
                    }

                    _unsetChoice($activeChoice);
                    if(targetSerial){
                        //swapping:
                        _unsetChoice($target);
                        _setChoice(_getChoice(targetSerial), $activeChoice);
                    }
                    _setChoice(_getChoice(choiceSerial), $target);

                    //always reset swapping mode after the choice is set
                    interaction.swapping = false;
                }

                _resetSelection();

            }else if($(this).data('serial')){

                //selecting a choice in editing mode:
                var serial = $(this).data('serial');

                $activeChoice = $(this);
                $activeChoice.addClass('active');

                $resultArea.find('>li>.target').filter(function(){
                    return $(this).data('serial') !== serial;
                }).addClass('empty');

                $choiceArea.find('>li:not(.deactivated)').filter(function(){
                    return $(this).data('serial') !== serial;
                }).addClass('empty');

                //append trash bin:
                var $bin = $('<span>', {'class' : 'icon-undo remove-choice', 'title' : __('remove')});
                $bin.on('mousedown', function(e){
                    e.stopPropagation();
                    _unsetChoice($activeChoice);
                    _resetSelection();
                });
                $(this).append($bin);
            }

        });

        //@todo run eyecatcher: fix it
//        eyecatcher();

        if(!interaction.responseMappingMode){
            _setInstructions(interaction);
        }
        
        _adaptSize(interaction);
    };

    var _setInstructions = function(interaction){

        var min = parseInt(interaction.attr('minAssociations')),
            max = parseInt(interaction.attr('maxAssociations'));

        //infinite association:
        if(min === 0){
            if(max === 0){
                Helper.appendInstruction(interaction, __('You may make as many association pairs as you want.'));
            }
        }else{
            if(max === 0){
                Helper.appendInstruction(interaction, __('The maximum number of association is unlimited.'));
            }
            //the max value is implicit since the appropriate number of empty pairs have already been created
            var msg = __('You need to make') + ' ';
            msg += (min > 1) ? __('at least') + ' ' + min + ' ' + __('association pairs') : __('one association pair');
            Helper.appendInstruction(interaction, msg, function(){
                if(_getRawResponse(interaction).length >= min){
                    this.setLevel('success');
                }else{
                    this.reset();
                }
            });
        }
    };

    var resetResponse = function(interaction){
        Helper.getContainer(interaction).find('.result-area>li>div').each(function(){
            unsetChoice(interaction, $(this));
        });
    };

    var _setPairs = function(interaction, pairs){

        var addedPairs = 0,
            $emptyPair = Helper.getContainer(interaction).find('.result-area>li:first');
        if(pairs && interaction.getResponseDeclaration().attr('cardinality') === 'single' && pairs.length){
            pairs = [pairs];
        }
        _.each(pairs, function(pair){
            if($emptyPair.length){
                var $divs = $emptyPair.children('div');
                setChoice(interaction, getChoice(interaction, pair[0]), $($divs[0]));
                setChoice(interaction, getChoice(interaction, pair[1]), $($divs[1]));
                addedPairs++;
                $emptyPair = $emptyPair.next('li');
            }else{
                //the number of pairs exceeds the maxium allowed pairs: break;
                return false;
            }
        });

        return addedPairs;
    };

    /**
     * Set the response to the rendered interaction.
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10291
     * 
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        _setPairs(interaction, pciResponse.unserialize(response, interaction));
    };

    var _getRawResponse = function(interaction){
        var response = [];
        Helper.getContainer(interaction).find('.result-area>li').each(function(){
            var pair = [];
            $(this).find('div').each(function(){
                var serial = $(this).data('serial');
                if(serial){
                    pair.push(interaction.getChoice(serial).id());
                }
            });
            if(pair.length === 2){
                response.push(pair);
            }
        });
        return response;
    };

    /**
     * Return the response of the rendered interaction
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10291
     * 
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    var destroy = function(interaction){

        var $container = Helper.getContainer(interaction);

        //destroy seelcted choice:
        $container.find('.result-area .active').mousedown();

        //remove event
        $(document).off('.commonRenderer');
        $container.find('.choice-area, .result-area').andSelf().off('.commonRenderer');

        //destroy response
        resetResponse(interaction);

        //remove instructions
        Helper.removeInstructions(interaction);

        Helper.getContainer(interaction).find('.result-area').empty();
    };

    return {
        qtiClass : 'associateInteraction',
        template : tpl,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy, //@todo to be renamed into destroy
        renderEmptyPairs : renderEmptyPairs
    };
});
