define(['jquery', 'i18n', 'helpers', 'context'], function($, __, helpers, context){
    
    return {
        
        start : function (){
            
            var saveUrl = helpers._url('saveLists', 'Lists', 'tao');
            var delListUrl = helpers._url('removeList', 'Lists', 'tao');
            var delEltUrl = helpers._url('removeListElement', 'Lists', 'tao');

            $(".list-editor").click(function(){
                    var uri = $(this).attr('id').replace('list-editor_', '');
                    var listContainer = $("div[id='list-data_" + uri+"']");

                    if(!listContainer.parent().is('form')){
                            listContainer.wrap("<form class='listbox' />");
                            listContainer.prepend("<input type='hidden' name='uri' value='"+uri+"' />");

                            $("<input type='text' name='label' value='"+listContainer.find('legend span').text()+"'/>").prependTo(listContainer.find('div.list-elements')).keyup(function(){
                                    listContainer.find('legend span').text($(this).val());
                            });

                            if (listContainer.find('.list-element').length){
                                    listContainer.find('.list-element').replaceWith(function(){
                                            return "<input type='text' name='" + $(this).attr('id') + "' value='"+$(this).text()+"' />";
                                    });
                            }

                            var elementList = listContainer.find('ol');
                            elementList.addClass('sortable-list');
                            elementList.find('li').addClass('ui-state-default');
                            elementList.find('li').prepend('<span class="ui-icon ui-icon-grip-dotted-vertical" ></span>');
                            elementList.find('li').prepend('<span class="ui-icon ui-icon-arrowthick-2-n-s" ></span>');
                            elementList.find('li').append('<span class="ui-icon ui-icon-circle-close list-element-deletor" style="cursor:pointer;" ></span>');

                            elementList.sortable({
                                    axis: 'y',
                                    opacity: 0.6,
                                    placeholder: 'ui-state-error',
                                    tolerance: 'pointer',
                                    update: function(event, ui){
                                            var map = {};
                                            $.each($(this).sortable('toArray'), function(index, id){
                                                    map[id] = 'list-element_' + (index + 1);
                                            });
                                            $(this).find('li').each(function(){
                                                    var id = $(this).attr('id');
                                                    if(map[id]){
                                                            $(this).attr('id', map[id]);
                                                            var newName = $(this).find('input').attr('name').replace(id, map[id]);
                                                            $(this).find('input').attr('name', newName);
                                                    }
                                            });
                                    }
                            });

                            var elementSaver = $("<a href='#'><img src='" +  context.base_www + "img/save.png' class='icon' />" + __('Save') + "</a>");
                            elementSaver.click(function(){1
                                    $.postJson(
                                            saveUrl,
                                            $(this).parents('form').serializeArray(),
                                            function(response){
                                                    if(response.saved){
                                                            helpers.createInfoMessage(__("list saved"));
                                                            helpers._load(helpers.getMainContainerSelector(), helpers._url('index', 'Lists', 'tao'));
                                                    }
                                            }
                                    );
                            });
                            elementList.after(elementSaver);

                            elementList.after('<br />');

                            var elementAdder = $("<a href='#'><img src='" +  context.base_www + "img/add.png' class='icon' />" + __('New element') + "</a>");
                            elementAdder.click(function(){
                                    var level = $(this).parent().find('ol').children().length + 1;
                                    $(this).parent().find('ol').append(
                                            "<li id='list-element_"+level+"' class='ui-state-default'>" +
                                                    "<span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>" +
                                                    "<span class='ui-icon ui-icon-grip-dotted-vertical' ></span>" +
                                                    "<input type='text' name='list-element_"+level+"_' />" +
                                                    "<span class='ui-icon ui-icon-circle-close list-element-deletor' ></span>" +
                                            "</li>");
                            });
                            elementList.after(elementAdder);
                    }

                    $(".list-element-deletor").click(function(){
                            if(confirm(__("Please confirm you want to delete this list element."))){
                                    var element = $(this).parent();
                                    uri = element.find('input:text').attr('name').replace(/^list\-element\_([1-9]*)\_/, '');
                                    $.postJson(
                                            delEltUrl,
                                            {uri: uri},
                                            function(response){
                                                    if(response.deleted){
                                                            element.remove();
                                                            helpers.createInfoMessage(__("element deleted"));
                                                    }
                                            }
                                    );
                            }
                    });
            });

            $(".list-deletor").click(function(){
                    if(confirm(__("Please confirm you want to delete this list. This operation is not reversible."))){
                            var list = $(this).parents("div.listbox");
                            var uri = $(this).attr('id').replace('list-deletor_', '');
                            $.postJson(
                                    delListUrl,
                                    {uri: uri},
                                    function(response){
                                            if(response.deleted){
                                                    helpers.createInfoMessage(__("list deleted"));
                                                    list.remove();
                                            }
                                    }
                            );
                    }
            });
        }
    };
});


