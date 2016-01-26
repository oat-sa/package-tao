<?php use oat\tao\helpers\Template;?>
<div class="permission-container flex-container-full">
    <h1><?= __('Access Permissions for') ?> <em><?= get_data('label') ?></em></h1>

    <form action="<?= _url('savePermissions') ?>" method="POST" class="list-container">
        <input type="hidden" name="resource_id" id="resource_id" value="<?= get_data('uri') ?>">

        <div class="permission-tabs">
            <ul>
                <li><a href="#tab-users"><?= __('Users') ?></a></li>
                <li><a href="#tab-roles"><?= __('Roles') ?></a></li>
            </ul>
            <div id="tab-users" class="permission-tabs-panel">
                <div class="grid-container msg-edit-area">
                    <div class="grid-row commit">
                        <label class="col-2"><span><?= __('Users') ?></span></label>
                        <div class="col-10 txt-rgt">
                            <?= tao_helpers_Icon::iconAdd(); ?>
                            <input type="text" id="add-user" style="width:100%" placeholder="<?= __('Add user(s)') ?>"
                                   data-url="<?= _url('search', 'Search', 'tao') ?>"
                                   data-ontology="http://www.tao.lu/Ontologies/TAO.rdf#User"
                                   data-params-root="params" />
                        </div>
                    </div>
                </div>

                <table class="matrix" id="permissions-table-users">
                    <colgroup>
                        <col class="cell-name">
                        <col class="cell-type">
                        <col class="cell-privilege" span="<?= count(get_data('privileges')) ?>">
                        <col class="cell-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Type') ?></th>
                            <?php foreach (get_data('privileges') as $privilegeLabel):?>
                            <th><?= ucfirst($privilegeLabel) ?></th>
                            <?php endforeach;?>
                            <th><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_data('users') as $uri => $user):?>
                        <tr>
                            <td><?= $user['label'] ?></td>
                            <td>
                                <?= __('user') ?>
                                <input type="hidden" name="users[<?= $uri ?>][type]" value="user">
                            </td>
                            <?php foreach (get_data('privileges') as $privilege => $privilegeLabel):?>
                            <td>
                                <label class="tooltip">
                                    <input type="checkbox" class="privilege-<?= $privilege ?>" name="users[<?= $uri ?>][<?= $privilege ?>]" value="1" <?= (in_array($privilege, $user['privileges'])) ? 'checked' : '' ?>>
                                    <span class="icon-checkbox"></span>
                                </label>
                            </td>
                            <?php endforeach;?>
                            <td>
                                <button type="button" class="small delete_permission tooltip btn-warning" data-acl-user="<?= $uri ?>" data-acl-type="user" data-acl-label="<?= $user['label'] ?>" >
                                    <span class="icon-bin"></span><?= __('Remove') ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>

            <div id="tab-roles" class="permission-tabs-panel">
                <div class="grid-container msg-edit-area">
                    <div class="grid-row commit">
                        <label class="col-2"><span><?= __('Roles') ?></span></label>
                        <div class="col-10 txt-rgt">
                            <?= tao_helpers_Icon::iconAdd(); ?>
                            <input type="text" id="add-role" style="width:100%" placeholder="<?= __('Add role(s)') ?>"
                                   data-url="<?= _url('search', 'Search', 'tao') ?>"
                                   data-ontology="http://www.tao.lu/Ontologies/generis.rdf#ClassRole"
                                   data-params-root="params" />
                        </div>
                    </div>
                </div>

                <table class="matrix" id="permissions-table-roles">
                    <colgroup>
                        <col class="cell-name">
                        <col class="cell-type">
                        <col class="cell-privilege" span="<?= count(get_data('privileges')) ?>">
                        <col class="cell-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Type') ?></th>
                            <?php foreach (get_data('privileges') as $privilegeLabel):?>
                            <th><?= ucfirst($privilegeLabel) ?></th>
                            <?php endforeach;?>
                            <th><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (get_data('roles') as $uri => $role):?>
                        <tr>
                            <td><?= $role['label'] ?></td>
                            <td>
                                <?= __('role') ?>
                                <input type="hidden" name="users[<?= $uri ?>][type]" value="role">
                            </td>
                            <?php foreach (get_data('privileges') as $privilege => $privilegeLabel):?>
                            <td>
                                <label class="tooltip">
                                    <input type="checkbox" class="privilege-<?= $privilege ?>" name="users[<?= $uri ?>][<?= $privilege ?>]" value="1" <?= (in_array($privilege, $role['privileges'])) ? 'checked' : '' ?>>
                                    <span class="icon-checkbox"></span>
                                </label>
                            </td>
                            <?php endforeach;?>
                            <td>
                                <button type="button" class="small delete_permission tooltip btn-warning" data-acl-user="<?= $uri ?>" data-acl-type="user" data-acl-label="<?= $role['label'] ?>" >
                                    <span class="icon-bin"></span><?= __('Remove') ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bottom-bar txt-rgt">
            <?php if (get_data('isClass')): ?>
            <label>
                <?=__('Recursive') ?>
                <input type="checkbox" name="recursive" value="1">
                <span class="icon-checkbox"></span>
            </label>
            <?php endif; ?>
            <button type="submit" class="btn-info small"><span class="icon-save"></span> <?= __('Save') ?></button>
        </div>
    </form>
</div>
