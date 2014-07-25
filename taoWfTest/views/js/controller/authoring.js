/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['module', 'jquery', 'lodash', 'i18n', 'generis.tree.select', 'helpers'], function(module, $, _, __, GenerisTreeSelectClass, helpers) {
    
    /**
     * Wf test authoring controller
     * @exports taoWfTest/controller/authoring
     */
    var Controller = {
        
         /**
          * Start the controller, main entry method.
          * @public 
          * @param {Object} options
          */
         start : function(options){
            var self = this;

            var $sequenceContainer = $('#item-sequence');
            var $sequenceInfo = $('#item-sequence').prev('.elt-info');

            options = _.merge(module.config(), options || {});
            var sequence = options.sequence || [];
            var newSequence = {};
            var labels = options.labels || {};

            new GenerisTreeSelectClass('#item-tree', helpers._url('getTreeData', 'Items', 'taoWfTest'),{
                actionId: 'item',
                saveUrl: options.saveurl,
                paginate:	10,
                saveCallback: function (data){
                    newSequence = {};
                    sequence = [];
                    var uris = $.parseJSON(data.instances);
                    for (var attr in uris) {
                        if ($.inArray(uris[attr], sequence) === -1 && attr !== undefined) {
                            newSequence[parseInt(attr.replace('instance_', ''))+1] = 'item_'+ uris[attr];
                            sequence[parseInt(attr.replace('instance_', ''))+1] =  uris[attr];
                        }
                    }
                    self._buildItemList($sequenceContainer, newSequence, labels);
                    if ($('li', $sequenceContainer).length) {
                        $sequenceInfo.show();
                    } else {
                        $sequenceInfo.hide();
                    }
                },
                checkedNodes : sequence,
                serverParameters: {
                    openNodes: options.openNodes,
                    rootNode:  options.rootNode
                },
                callback: {
                    checkPaginate: function(NODE, TREE_OBJ) {
                        //Check the unchecked that must be checked... olè!
                        this.check(sequence);
                    }
                }
            });
            
            $sequenceContainer.sortable({
                axis: 'y',
                opacity: 0.6,
                placeholder: 'ui-state-error',
                tolerance: 'pointer',
                update: function(event, ui){
                    var index;
                    var listItems = $(this).sortable('toArray');

                    newSequence = {};
                    sequence = [];
                    for (var i = 0; i < listItems.length; i++) {
                            index = i+1;
                            newSequence[index] = listItems[i];
                            sequence[index] = listItems[i].replace('item_', '');
                    }
                    self._buildItemList($sequenceContainer, newSequence, labels);
                }
            });

            $sequenceContainer
              .on('mousedown', 'li', function(){
                $(this).css('cursor', 'move');
            })
              .on('mouseup', 'li', function(){
                $(this).css('cursor', 'pointer');
            });

            $("#saver-action-item-sequence").click(function(){
                var toSend = {};
                for(var index in sequence){
                    toSend['instance_'+index] = sequence[index];
                }
                toSend.uri = $("input[name=uri]").val();
                toSend.classUri = $("input[name=classUri]").val();
                $.ajax({
                    url: options.saveurl,
                    type: "POST",
                    data: toSend,
                    dataType: 'json',
                    success: function(response){
                            if (response.saved) {
                                helpers.createInfoMessage(__('Sequence saved successfully'));
                            }
                    },
                    complete: function(){
                        helpers.loaded();
                    }
                });
            });
        },

        _buildItemList : function ($container, items, labels){
            var html = '';
            var itemId;
            for (var i in items) {
                    itemId = items[i];
                    html += "<li class='ui-state-default' id='" + itemId + "' >";
                    html += "<span class='ui-icon ui-icon-arrowthick-2-n-s' /><span class='ui-icon ui-icon-grip-dotted-vertical' />";
                    html += i + ". " + labels[itemId];
                    html += "</li>";
            }
            $container.html(html);
        }
    };

    return Controller;
});
