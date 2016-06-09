<?php use oat\tao\helpers\Template; ?>
<link rel="stylesheet" href="<?= Template::css('revision.css') ?>"/>
<div class="revision-container flex-container-full">
    <h1><?= __('History of %s', get_data('resourceLabel')) ?></h1>

    <form action="<?= _url('commitResource') ?>" method="POST" class="list-container">
        <input type="hidden" name="id" id="resource_id" value="<?= get_data('id') ?>">

        <div class="grid-container msg-edit-area">
            <div class="grid-row commit">
                <label class="col-1 block txt-rgt">
                    <?= __('Message') ?>
                </label>
                <div class="col-10">
                    <input type="text" name="message" id="message">
                </div>
                <div class="col-1  txt-rgt">
                    <button type="submit" class="btn-info small"><?= __('Commit') ?></button>
                </div>
            </div>
        </div>

        <table class="matrix revision-table" id="revisions-table">
            <thead>
            <tr>
                <th><?= __('Id'); ?></th>
                <th><?= __('Time'); ?></th>
                <th><?= __('User'); ?></th>
                <th><?= __('Message'); ?></th>
                <th><?= __('Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (get_data('revisions') as $revision): ?>
                <tr>
                    <td class="numeric"><?= $revision['id'] ?></td>
                    <td class="date"><?= $revision['modified'] ?></td>
                    <td class="user"><?= $revision['author'] ?></td>
                    <td class="message"><?= $revision['message'] ?></td>
                    <td class="actions">
                        <button class="btn-info small restore_revision" data-revision="<?= $revision['id'] ?>" type="button">
                            <span class="icon-undo"></span>
                            <?= __('Restore')?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>
