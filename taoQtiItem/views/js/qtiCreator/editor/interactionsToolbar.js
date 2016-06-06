define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/editor/customInteractionRegistry',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/insertInteractionButton',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/insertInteractionGroup',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/tooltip',
    'ui/tooltipster'
], function($, _, __, ciRegistry, insertInteractionTpl, insertSectionTpl, tooltipTpl, tooltip){
    "use strict";
    /**
     * String to identify a custom interaction from the authoring data
     * 
     * @type String
     */
    var _customInteractionTag = 'Custom Interactions';

    /**
     * Interaction types that require a sub group in the toolbar
     * 
     * @type Object
     */
    var _subgroups = {
        'inline-interactions' : 'Inline Interactions'
    };

    var _events = {
        interactiontoolbarready : 'interactiontoolbarready.qti-widget'
    };

    function getGroupId(groupLabel){
        return groupLabel.replace(/\W+/g, '-').toLowerCase();
    }

    function getGroupSectionId(groupLabel){
        return 'sidebar-left-section-' + getGroupId(groupLabel);
    }

    function addGroup($sidebar, groupLabel){

        var groupId = getGroupSectionId(groupLabel);

        var $section = $(insertSectionTpl({
            id : groupId,
            label : groupLabel
        }));

        $sidebar.append($section);

        return $section;
    }
    
    function create($sidebar, interactions){

        _.each(interactions, function(interactionAuthoringData){
            add($sidebar, interactionAuthoringData);
        });

        buildSubGroups($sidebar);

        //set it ad "ready":
        $sidebar.data('interaction-toolbar-ready', true);
        $sidebar.trigger(_events.interactiontoolbarready);//interactiontoolbarready.qti-widget
    }

    function getGroup($sidebar, groupLabel){

        var groupId = getGroupSectionId(groupLabel);
        return $sidebar.find('#' + groupId);
    }

    function isReady($sidebar){

        return !!$sidebar.data('interaction-toolbar-ready');
    }

    function remove($sidebar, interactionClass){
        $sidebar.find('li[data-qti-class="' + interactionClass + '"]:not(.dev)').remove();
    }
    
    function exists($sidebar, interactionClass){
        return !!$sidebar.find('li[data-qti-class="' + interactionClass + '"]').length;
    }
    
    function add($sidebar, interactionAuthoringData){
        
        if(exists($sidebar, interactionAuthoringData.qtiClass)){
            throw 'the interaction is already in the sidebar';
        }
        
        var groupLabel = interactionAuthoringData.tags[0] || '',
            subGroupId = interactionAuthoringData.tags[1],
            $group = getGroup($sidebar, groupLabel),
            tplData = {
                qtiClass : interactionAuthoringData.qtiClass,
                disabled : !!interactionAuthoringData.disabled,
                title : interactionAuthoringData.description,
                iconFont : /^icon-/.test(interactionAuthoringData.icon),
                icon : interactionAuthoringData.icon,
                short : interactionAuthoringData.short,
                dev : (_customInteractionTag === groupLabel) && ciRegistry.isDev(interactionAuthoringData.qtiClass.replace('customInteraction.', ''))
            };

        if(subGroupId && _subgroups[subGroupId]){
            tplData.subGroup = subGroupId;
        }

        if(!$group.length){
            //the group does not exist yet : create a <section> for the group
            $group = addGroup($sidebar, groupLabel);
        }

        if(subGroupId && _subgroups[subGroupId]){
            tplData.subGroup = subGroupId;
        }

        if(!$group.length){
            //the group does not exist yet : create a <section> for the group
            $group = addGroup($sidebar, groupLabel);
        }
        
        var $interaction = $(insertInteractionTpl(tplData));
        $group.find('.tool-list').append($interaction);
        
        return $interaction;
    }

    function buildSubGroups($sidebar){

        $sidebar.find('[data-sub-group]').each(function(){

            var $element = $(this),
                $section = $element.parents('section'),
                subGroup = $element.data('sub-group'),
                $subGroupPanel,
                $subGroupList,
                $cover;

            if(!subGroup){
                return;
            }

            $subGroupPanel = $section.find('.sub-group.' + subGroup);
            $subGroupList = $subGroupPanel.find('.tool-list');
            if(!$subGroupPanel.length){
                $subGroupPanel = $('<div>', {'class' : 'panel clearfix sub-group ' + subGroup});
                $subGroupList = $('<ul>', {'class' : 'tool-list plain clearfix'});
                $subGroupPanel.append($subGroupList);
                $section.append($subGroupPanel);
                $cover = $('<div>', {'class' : 'sub-group-cover blocking'});
                $subGroupPanel.append($cover);
                $subGroupPanel.data('cover', $cover);
            }
            $subGroupList.append($element);
        });

        addInlineInteractionTooltip();
    }

    /**
     * add tooltip to explain special requirement and behaviours for inline interactions
     * may need to generalize it in the future
     */
    function addInlineInteractionTooltip(){

        var timer,
            $inlineInteractionsPanel = $('#sidebar-left-section-inline-interactions .inline-interactions'),
            $tooltip = $(tooltipTpl({
                message : __('Inline interactions need to be inserted into a text block.')
            }));

        $inlineInteractionsPanel.append($tooltip);
        tooltip($inlineInteractionsPanel);

        $tooltip.css({
            position : 'absolute',
            zIndex : 11,
            top : 0,
            right : 10
        });

        $inlineInteractionsPanel.on('mouseenter', '.sub-group-cover', function(){

            timer = setTimeout(function(){
                $tooltip.find('[data-tooltip]').tooltipster('show');
            }, 300);

        }).on('mouseleave', '.sub-group-cover', function(){

            $tooltip.find('[data-tooltip]').tooltipster('hide');
            clearTimeout(timer);
        });
    }

    return {
        create : create,
        add : add,
        exists : exists,
        addGroup : addGroup,
        getGroupId : getGroupId,
        getGroupSectionId : getGroupSectionId,
        getGroup : getGroup,
        isReady : isReady,
        remove : remove,
        getCustomInteractionTag : function(){
            return _customInteractionTag;
        }
    };

});