define([
    'lodash',
    'jquery',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/orderInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'i18n',
    'jqueryui'
], function(_, $, tpl, Helper, pciResponse, __){

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
     * 
     * @param {object} interaction
     */
    var render = function(interaction){

        var $container = Helper.getContainer(interaction),
            $choiceArea = $container.find('.choice-area'),
            $resultArea = $container.find('.result-area'),
            $iconAdd = $container.find('.icon-add-to-selection'),
            $iconRemove = $container.find('.icon-remove-from-selection'),
            $iconBefore = $container.find('.icon-move-before'),
            $iconAfter = $container.find('.icon-move-after'),
            $activeChoice = null;

        var _activeControls = function(){
            $iconAdd.addClass('inactive');
            $iconRemove.removeClass('inactive').addClass('active');
            $iconBefore.removeClass('inactive').addClass('active');
            $iconAfter.removeClass('inactive').addClass('active');
        };

        var _resetControls = function(){
            $iconAdd.removeClass('inactive');
            $iconRemove.removeClass('active').addClass('inactive');
            $iconBefore.removeClass('active').addClass('inactive');
            $iconAfter.removeClass('active').addClass('inactive');
        };

        var _setSelection = function($choice){
            if($activeChoice){
                $activeChoice.removeClass('active');
            }
            $activeChoice = $choice;
            $activeChoice.addClass('active');
            _activeControls();
        };

        var _resetSelection = function(){
            if($activeChoice){
                $activeChoice.removeClass('active');
                $activeChoice = null;
            }
            _resetControls();
        };
        
        $container.on('mousedown.commonRenderer', function(e){
            _resetSelection();
        });
        
        $choiceArea.on('mousedown.commonRenderer', '>li:not(.deactivated)', function(e){
            
            e.stopPropagation();
            
            _resetSelection();

            $iconAdd.addClass('triggered');
            setTimeout(function(){
                $iconAdd.removeClass('triggered');
            }, 150);

            //move choice to the result list:
            $resultArea.append($(this));
            Helper.triggerResponseChangeEvent(interaction);

            //update constraints :
            Helper.validateInstructions(interaction);
        });

        $resultArea.on('mousedown.commonRenderer', '>li', function(e){
            
            e.stopPropagation();
            
            var $choice = $(this);
            if($choice.hasClass('active')){
                _resetSelection();
            }else{
                _setSelection($(this));
            }
        });

        $iconRemove.on('mousedown.commonRenderer', function(e){
            
            e.stopPropagation();
            
            if($activeChoice){

                //restore choice back to choice list
                $choiceArea.append($activeChoice);
                Helper.triggerResponseChangeEvent(interaction);

                //update constraints :
                Helper.validateInstructions(interaction);
            }

            _resetSelection();
        });

        $iconBefore.on('mousedown.commonRenderer', function(e){
            
            e.stopPropagation();
            
            var $prev = $activeChoice.prev();
            if($prev.length){
                $prev.before($activeChoice);
                Helper.triggerResponseChangeEvent(interaction);
            }
        });

        $iconAfter.on('mousedown.commonRenderer', function(e){
            
            e.stopPropagation();
            
            var $next = $activeChoice.next();
            if($next.length){
                $next.after($activeChoice);
                Helper.triggerResponseChangeEvent(interaction);
            }
        });

        _setInstructions(interaction);

        //bind event listener in case the attributes change dynamically on runtime
        $(document).on('attributeChange.qti-widget.commonRenderer', function(e, data){
            if(data.element.getSerial() === interaction.getSerial()){
                if(data.key === 'maxChoices' || data.key === 'minChoices'){
                    Helper.removeInstructions(interaction);
                    _setInstructions(interaction);
                    Helper.validateInstructions(interaction);
                }
            }
        });
        
        _freezeSize($container);
    };
    
    var _freezeSize = function($container){
        var $orderArea = $container.find('.order-interaction-area');
        $orderArea.height($orderArea.height());
    };
    
    var _setInstructions = function(interaction){

        var $choiceArea = Helper.getContainer(interaction).find('.choice-area'),
            $resultArea = Helper.getContainer(interaction).find('.result-area'),
            min = parseInt(interaction.attr('minChoices')),
            max = parseInt(interaction.attr('maxChoices'));

        if(min){
            Helper.appendInstruction(interaction, __('You must use at least ' + min + ' choices'), function(){
                if($resultArea.find('>li').length >= min){
                    this.setLevel('success');
                }else{
                    this.reset();
                }
            });
        }

        if(max && max < _.size(interaction.getChoices())){
            var instructionMax = Helper.appendInstruction(interaction, __('You can use maximum ' + max + ' choices'), function(){
                if($resultArea.find('>li').length >= max){
                    $choiceArea.find('>li').addClass('deactivated');
                    this.setMessage(__('Maximum choices reached'));
                }else{
                    $choiceArea.find('>li').removeClass('deactivated');
                    this.reset();
                }
            });

            $choiceArea.on('mousedown.commonRenderer', '>li.deactivated', function(){
                var $choice = $(this);
                $choice.addClass('brd-error');
                instructionMax.setLevel('warning', 2000);
                setTimeout(function(){
                    $choice.removeClass('brd-error');
                }, 150);
            });
        }
    };

    var _resetResponse = function(interaction){

        var initialOrder = _.keys(interaction.getChoices());
        var $choiceArea = Helper.getContainer(interaction).find('.choice-area').append(Helper.getContainer(interaction).find('.result-area>li'));
        var $choices = $choiceArea.children('.qti-choice');
        $choices.detach().sort(function(choice1, choice2){
            return (_.indexOf(initialOrder, $(choice1).data('serial')) > _.indexOf(initialOrder, $(choice2).data('serial')));
        });
        $choiceArea.prepend($choices);
    };

    /**
     * Set the response to the rendered interaction.
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
     * 
     * Special value: the empty object value {} resets the interaction responses
     * 
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var $choiceArea = Helper.getContainer(interaction).find('.choice-area'),
            $resultArea = Helper.getContainer(interaction).find('.result-area');

        if(response === null || _.isEmpty(response)){
            _resetResponse(interaction);
        }else{
            try{
                _.each(pciResponse.unserialize(response, interaction), function(identifier){
                    $resultArea.append($choiceArea.find('[data-identifier=' + identifier + ']'));
                });
            }catch(e){
                throw new Error('wrong response format in argument : ' + e);
            }
        }

        Helper.validateInstructions(interaction);
    };

    var _getRawResponse = function(interaction){
        var response = [];
        Helper.getContainer(interaction).find('.result-area>li').each(function(){
            response.push($(this).data('identifier'));
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
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
     * 
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    var getCustomData = function(interaction, data){

        return _.merge(data || {}, {
            horizontal : (interaction.attr('orientation') === 'horizontal')
        });

    };

    var destroy = function(interaction){
        
        var $container = Helper.getContainer(interaction);
        $container.find('.qti-choice.active').mousedown();

        //first, remove all events
        var selectors = [
            '.choice-area',
            '.result-area',
            '.icon-add-to-selection',
            '.icon-remove-from-selection',
            '.icon-move-before',
            '.icon-move-after'
        ];
        $container.find(selectors.join(',')).andSelf().off('.commonRenderer');
        $(document).off('.commonRenderer');
        
        $container.find('.order-interaction-area').removeAttr('style');
        
        _resetResponse(interaction);

        Helper.removeInstructions(interaction);
    };

    return {
        qtiClass : 'orderInteraction',
        getData : getCustomData,
        template : tpl,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        destroy : destroy
    };
});