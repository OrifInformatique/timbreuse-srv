<?php $isAdminView = url_is('*admin*'); ?>
<div class="container">
    <div class="row mb-2">
        <div class="text-left col-12">
            <h3><?= esc($title) ?></h3>
        </div>
        <?php if (isset($createUrl)): ?>
            <div class="col-sm-6 text-left">
                <a class="btn btn-primary" href="<?= $createUrl ?>"><?= lang('common_lang.btn_add') ?></a>
            </div>
        <?php endif; ?>
    </div>

    <table class="table table-hover tree-table">
        <thead>
            <th><?= lang('tim_lang.field_name') ?></th>
            <th></th>
        </thead>
        <tbody id="table-body">
            <?php foreach($userGroups as $userGroup): ?>
                <tr class="<?= isset($userGroup['class']) ? $userGroup['class'] : '' ?>">
                    <td>
                        <?= $userGroup['name'] ?>
                    </td>
                    <td class="text-right">
                        <?php if (isset($userGroup['addUrl'])): ?>
                            <a href="<?= $userGroup['addUrl'] ?>">
                                <i class="bi bi-plus-circle" style="font-size: 20px;"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (isset($userGroup['updateUrl'])): ?>
                            <a href="<?= $userGroup['updateUrl'] ?>">
                                <i class="bi bi-pencil" style="font-size: 20px;"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (isset($userGroup['deleteUrl'])): ?>
                            <a href="<?= $userGroup['deleteUrl'] ?>">
                                <i class="bi bi-trash" style="font-size: 20px;"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
