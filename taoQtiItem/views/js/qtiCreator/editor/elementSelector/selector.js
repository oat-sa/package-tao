define([
    'jquery',
    'lodash',
    'tpl!taoQtiItem/qtiCreator/editor/elementSelector/tpl/popup',
    'tpl!taoQtiItem/qtiCreator/editor/elementSelector/tpl/content'
], function($, _, popupTpl, contentTpl){

    var _ns = '.element-selector';
    
    /**
     * Create an element selector reltive to the $anchor and contained in the $container
     * 
     * @param {JQuery} $anchor
     * @param {JQuery} $container
     * @param {Array} interactions - the list of authorable interactions
     * @returns {Object} the new selector instance
     */
    function create($anchor, $container, interactions){
        
        //anchor must be positioned in css
        var positions = _computePosition($anchor, $container);
        var $element = $(popupTpl({
            popup : positions.popup,
            arrow : positions.arrow,
            content : _renderContent(interactions)
        }));

        //only one 
        $anchor.find('.contextual-popup').remove();

        //style and attach the form
        $anchor.append($element);

        $element.off(_ns).on('click' + _ns, '.group-list li', function(){
            var $trigger = $(this);
            _activatePanel($element, $trigger);
        }).on('click' + _ns, '.element-list li', function(){
            _activateElement($element, $(this));
        }).on('click' + _ns, '.done', function(){
            _done($element);
        }).on('click' + _ns, '.cancel', function(){
            _cancel($element);
        });

        return {
            getPopup : function(){
                return $element;
            },
            reposition : function(){
                var pos = _computePosition($anchor, $container);
                $element.css({
                    top : pos.popup.top,
                    left : pos.popup.left
                });
                $element.children('.arrow').css('left', pos.arrow.left);
                $element.children('.arrow-cover').css('left', pos.arrow.leftCover);
            },
            activatePanel : function(groupName){
                activatePanel($element, groupName);
            },
            activateElement : function(qtiClass){
                activateElement($element, qtiClass);
            },
            done : function(){
                _done($element);
            },
            cancel : function(){
                _cancel($element);
            },
            show : function(){
                $element.show();
            },
            destroy : function(){
                $element.remove();
            }
        };
    }
    
    /**
     * Callback when the "done" button is clicked
     * 
     * @param {JQuery} $element
     */
    function _done($element){
        $element.hide();
        $element.trigger('done' + _ns);
    }
    
    /**
     * Callback when the "cancel" button is clicked
     * 
     * @param {JQuery} $element
     */
    function _cancel($element){
        $element.hide();
        $element.trigger('cancel' + _ns);
    }
    
    /**
     * Activate the panel defined by the groupName
     * 
     * @param {JQuery} $container
     * @param {String} groupName
     */
    function activatePanel($container, groupName){
        var $trigger = $container.find('.group-list li[data-group-name="' + groupName + '"]');
        _activatePanel($container, $trigger);
    }
    
    /**
     * Activate a panel by its trigger button in the navigation tab
     * 
     * @param {JQuery} $container
     * @param {JQuery} $trigger
     */
    function _activatePanel($container, $trigger){
        if(!$trigger.hasClass('active')){
            $trigger.addClass('active').siblings('.active').removeClass('active');
            var group = $trigger.data('group-name');
            var $group = $container.find('.element-group[data-group-name="' + group + '"]');
            $group.show().siblings('.element-group').hide();
        }
    }
    
    /**
     * Activate an element identified by its qti class
     * 
     * @param {JQuery} $container
     * @param {String} qtiClass
     */
    function activateElement($container, qtiClass){
        var $trigger = $container.find('.element-list li[data-qti-class="' + qtiClass + '"]');
        _activateElement($container, $trigger);
    }
    
    /**
     * Activate an element identified by its $trigger dom element
     * 
     * @param {JQuery} $container
     * @param {JQuery} $trigger
     */
    function _activateElement($container, $trigger){
        var qtiClass = $trigger.data('qti-class');
        if(!$trigger.hasClass('active')){
            $container.find('.element-list li').removeClass('active');
            $trigger.addClass('active');
            $container.trigger('selected' + _ns, [qtiClass, $trigger]);
        }
    }
    
    /**
     * Calculate the position of the popup and arrow relative to the anchor and container elements
     * 
     * @param {JQuery} $anchor
     * @param {JQuery} $container
     * @returns {Object} - Object containing the positioning data
     */
    function _computePosition($anchor, $container){
        
        var popupWidth = 500;
        var arrowWidth = 6;
        var marginTop = 15;
        var marginLeft = 15;
        var _anchor = {top : $anchor.offset().top, left : $anchor.offset().left, w : $anchor.innerWidth(), h : $anchor.innerHeight()};
        var _container = {top : $container.offset().top, left : $container.offset().left, w : $container.innerWidth()};
        var _popup = {
            top : _anchor.h + marginTop,
            left : -popupWidth / 2 + _anchor.w/2,
            w : popupWidth
        };
        
        var offset = _anchor.left - _container.left;
        //do we have enough space on the left ?
        if(offset + marginLeft + _anchor.w/2 < _popup.w / 2){
            _popup.left = -offset + marginLeft;
        }else if(_container.w - (offset + _anchor.w/2 + marginLeft) < _popup.w / 2){
            _popup.left = -offset + _container.w - marginLeft - _popup.w;
        }

        var _arrow = {
            left : -_popup.left + _anchor.w / 2 - arrowWidth,
            leftCover : -_popup.left + _anchor.w / 2 - arrowWidth - 6
        };

        return {
            popup : _popup,
            arrow : _arrow
        };
    }
    
    /**
     * Filter and format the raw authorable interactions array into useful data for this selector
     * @param {Array} interactions
     * @returns {Array}
     */
    function _filterInteractions(interactions){
        var block;
        //remove all inline interactions, keep block container only
        var filtered = _.filter(interactions, function(interaction){
            var tags = interaction.tags;
            if(interaction.qtiClass === '_container'){
                block = interaction;
                interaction.tags[0] = 'Text Block';
                return false;
            }else if(tags && tags[0] !== 'Inline Interactions'){
                return true;
            }
            return false;
        });
        block.tags[0] = 'Text Block';
        filtered.unshift(block);
        return filtered;
    }
    
    /**
     * Render the content of the selector from the list of authorable interactions as data
     * 
     * @param {Array} interactions
     * @returns {String} 
     */
    function _renderContent(interactions){

        var groups = [];
        _.each(_filterInteractions(interactions), function(interaction){

            var groupName = interaction.tags[0];
            var panel = _.find(groups, {name : groupName});
            if(!panel){
                panel = {
                    name : groupName,
                    label : groupName.replace(/\sInteractions$/, ''),
                    elements : []
                };
                groups.push(panel);
            }

            panel.elements.push({
                qtiClass : interaction.qtiClass,
                disabled : !!interaction.disabled,
                title : interaction.description,
                iconFont : /^icon-/.test(interaction.icon),
                icon : interaction.icon,
                label : interaction.label
            });
        });

        return contentTpl({
            groups : groups
        });
    }

    return {
        create : create,
        activateElement : activateElement,
        activatePanel : activatePanel
    };
});