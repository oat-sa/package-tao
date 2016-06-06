define(['jquery', 'i18n', 'helpers', 'ui/feedback'], function ($, __, helpers, feedback) {
    'use strict';

    function _addSquareBtn(title, icon, $listToolBar) {
        var $btn = $('<button>', { 'class': 'btn-info small lft ' +  'icon-'+ icon, title: __(title) });
        $listToolBar.append($btn);
        return $btn;
    }

    return {

        start: function () {

            var saveUrl = helpers._url('saveLists', 'Lists', 'taoBackOffice');
            var delListUrl = helpers._url('removeList', 'Lists', 'taoBackOffice');
            var delEltUrl = helpers._url('removeListElement', 'Lists', 'taoBackOffice');

            $(".list-edit-btn").click(function () {
                var $btn = $(this),
                    uri = $btn.data('uri'),
                    $listContainer = $("#list-data_" + uri ),
                // form must be on the inside rather than on the outside as it has been in 2.6
                    $listForm     = $listContainer.find('form'),
                    $listTitleBar = $listContainer.find('.container-title h6'),
                    $listToolBar  = $listContainer.find('.data-container-footer'),
                    $listSaveBtn,
                    $listNewBtn;

                if (!$listForm.length) {

                    $listForm = $('<form>');
                    $listContainer.wrapInner($listForm);
                    $listContainer.find('form').append('<input type="hidden" name="uri" value="' + uri + '" />');

                    var $labelEdit = $("<input type='text' name='label' value=''/>").val($listTitleBar.text());
                    $listTitleBar.html($labelEdit);

                    if ($listContainer.find('.list-element').length) {
                        $listContainer.find('.list-element').replaceWith(function () {
                            return $("<input type='text' name='" + $(this).attr('id') + "' value='' />").val($(this).text());
                        });
                    }

                    var elementList = $listContainer.find('ol');
                    elementList.addClass('sortable-list');
                    elementList.find('li').prepend('<span class="icon-grip" ></span>');
                    elementList.find('li').append('<span class="icon-checkbox-crossed list-element-delete-btn" style="cursor:pointer;" ></span>');

                    elementList.sortable({
                        axis: 'y',
                        opacity: 0.6,
                        placeholder: 'ui-state-error',
                        tolerance: 'pointer',
                        update: function (event, ui) {
                            var map = {};
                            $.each($(this).sortable('toArray'), function (index, id) {
                                map[id] = 'list-element_' + (index + 1);
                            });
                            $(this).find('li').each(function () {
                                var id = $(this).attr('id');
                                if (map[id]) {
                                    $(this).attr('id', map[id]);
                                    var newName = $(this).find('input').attr('name').replace(id, map[id]);
                                    $(this).find('input').attr('name', newName);
                                }
                            });
                        }
                    });

                    $listSaveBtn = _addSquareBtn('Save element', 'save', $listToolBar);
                    $listSaveBtn.on('click', function () {
                        $.postJson(
                            saveUrl,
                            $(this).closest('form').serializeArray(),
                            function (response) {
                                if (response.saved) {
                                    feedback().success(__('"list saved"'));
                                    helpers._load(helpers.getMainContainerSelector(), helpers._url('index', 'Lists', 'taoBackOffice'));
                                }else{
                                    feedback().error(__('List not saved'));
                                }
                            }
                        );
                        return false;
                    });

                    $listNewBtn = _addSquareBtn('New element', 'add', $listToolBar);
                    $listNewBtn.click(function () {
                        var level = $(this).closest('form').find('ol').children().length + 1;
                        $(this).closest('form').find('ol').append(
                            "<li id='list-element_" + level + "'>" +
                            "<span class='icon-grip' ></span>" +
                            "<input type='text' name='list-element_" + level + "_' />" +
                            "<span class='icon-add list-element-delete-btn' ></span>" +
                            "</li>");
                        return false;
                    });
                }

                $(".list-element-delete-btn").click(function () {
                    var $btn = $(this),
                        uri = $btn.data('uri');
                    if (confirm(__("Please confirm you want to delete this list element."))) {
                        var element = $(this).parent();
                        uri = element.find('input:text').attr('name').replace(/^list\-element\_([1-9]*)\_/, '');
                        $.postJson(
                            delEltUrl,
                            {uri: uri},
                            function (response) {
                                if (response.deleted) {
                                    element.remove();
                                    feedback().success(__('Element deleted'));
                                }else{
                                    feedback().error(__('Element not deleted'));
                                }
                            }
                        );
                    }
                });
            });

            $('.list-delete-btn').click(function () {
                if (confirm(__("Please confirm you want to delete this list. This operation cannot be undone."))) {
                    var $btn = $(this),
                        uri = $btn.data('uri'),
                        $list = $(this).parents(".data-container");
                    $.postJson(
                        delListUrl,
                        {uri: uri},
                        function (response) {
                            if (response.deleted) {
                                feedback().success(__('List deleted'));
                                $list.remove();
                            }else{
                                feedback().error(__('List not deleted'));
                            }
                        }
                    );
                }
            });
        }
    };
});
