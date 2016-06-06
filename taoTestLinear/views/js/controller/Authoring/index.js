/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'module', 
    'jquery', 
    'lodash', 
    'i18n', 
    'generis.tree.select', 
    'helpers', 
    'ui/feedback'
],  function(module, $, _, __, GenerisTreeSelectClass, helpers, feedback) {
    
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

            options = _.merge(module.config(), options || {});

            var sequence = options.sequence || [];
            var newSequence = {};
            var labels = options.labels || {};

            new GenerisTreeSelectClass('#item-tree', helpers._url('getData', 'GenerisTree', 'tao'),{
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

            //TODO use propert css classes            
            $sequenceContainer
              .on('mouseover', 'li', function(){
                $(this).css('cursor', 'grab');
            })
              .on('mousedown', 'li', function(){
                $(this).css('cursor', 'grabbing');
            })
              .on('mouseup', 'li', function(){
                $(this).css('cursor', 'pointer');
            });

            $sequenceContainer.sortable({
                axis: 'y',
                opacity: 0.6,
                placeholder: 'placeholder',
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

            $(".sequence-container .saver").click(function(){
                var toSend = {};
                for(var index in sequence){
                    toSend['instance_'+index] = sequence[index];
                }

                $('[id*="config_"]').each(function(){
                    var $config = $(this);
                    var key = $config.attr('id').replace('config_','');
                    toSend[key] = $config.find('input').prop('checked');
                });

                toSend.uri = $("input[name=uri]").val();
                toSend.classUri = $("input[name=classUri]").val();

                $.ajax({
                    url: options.saveurl,
                    type: "POST",
                    data: toSend,
                    dataType: 'json',
                    success: function(response){
                            if (response.saved) {
                                feedback().success(__('Sequence saved successfully'));
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
                html += i + ". " + labels[itemId];
                html += "</li>";
            }
            $container.html(html);
        }
    };

    return Controller;
});
