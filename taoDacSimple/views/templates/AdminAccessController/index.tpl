<?php use oat\tao\helpers\Template;?>
<div class="permission-container flex-container-full">
    <?php
        $userData = get_data('userData');
    ?>
    <h1><?= __('Access Permissions for')?> <em><?= get_data('label')?></em></h1>

    <form action="<?=_url('savePermissions')?>" method="POST" class="grid-container">
        <input type="hidden" name="resource_id" id="resource_id" value="<?= get_data('uri')?>">
        <table class="matrix" id="permissions-table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><?= __('Type');?></th>
                    <?php foreach (get_data('privileges') as $privilegeLabel):?>
                        <th><?= $privilegeLabel?></th>
                    <?php endforeach;?>
                    <th><?= __('Actions')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (get_data('userPrivileges') as $userUri => $privileges):?>
                <tr>
                    <td><?= $userData[$userUri]['label']?></td>
                    <td>
                        <?= $userData[$userUri]['isRole'] ? 'role' : 'user' ?>
                        <input type="hidden" name="users[<?= $userUri?>][type]" value="<?=  $userData[$userUri]['isRole'] ? 'role' : 'user'?>">
                    </td>
                    <?php foreach (get_data('privileges') as $privilege => $privilegeLabel):?>
                        <td>
                            <label class="tooltip">
                                <input type="checkbox" class="privilege-<?= $privilege?>" name="users[<?= $userUri?>][<?= $privilege?>]" value="1" <?= (in_array($privilege, $privileges)) ? 'checked' : '' ?>>
                                <span class="icon-checkbox"></span>
                            </label>
                        </td>
                    <?php endforeach;?>
                    <td>
                        <button type="button" class="small delete_permission tooltip btn-link" data-acl-user="<?= $userUri?>" data-acl-type="<?= $userData[$userUri]['isRole'] ? 'role' : 'user'?>" data-acl-label="<?= $userData[$userUri]['label']?>" >
                            <span class="icon-remove"></span><?= __('Remove')?>
                        </button>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <div class="grid-row">
            <div class="col-3">
                <select id="add-user" multiple style="width:100%">
                    <?php foreach ($users as $userId => $username):?>
                    <option value="<?=$userId?>"><?=$username?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="add">
                <button class="btn-info small" id="add-user-btn" type="button"><?= __('Add user(s)')?></button>
            </div>
            <div class="col-3">
                <select id="add-role" multiple style="width:100%">
                    <?php foreach ($roles as $roleId => $roleLabel):?>
                    <option value="<?=$roleId?>"><?=$roleLabel?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="add">
                <button class="btn-info small" id="add-role-btn" type="button"><?= __('Add role(s)')?></button>
            </div>
            <div class="col-3 txt-rgt">
                <label>
                    <?=__('Recursive')?>
                    <input type="checkbox" name="recursive" value="1">
                    <span class="icon-checkbox"></span>
                </label>
                <button type="submit" class="btn-info small"><?= __('Save')?></button>
            </div>
        </div>
    </form>
</div>
