<div class="container">
    <div class="row mb-2">
        <div class="text-left col-12">
            <h3><?= esc($title) ?></h3>
        </div>
        <div class="col-sm-6 text-left">
            <a class="btn btn-primary" href="<?= current_url() . '/create' ?>"><?= lang('common_lang.btn_add') ?></a>
        </div>
    </div>

    <table class="table table-hover tree-table">
        <thead>
            <th><?= lang('tim_lang.field_name') ?></th>
            <th></th>
        </thead>
        <tbody id="table-body">
            <?php foreach($userGroups as $userGroup): ?>
                <tr class="<?= is_null($userGroup['fk_parent_user_group_id']) ? 'bg-light' : '' ?>">
                    <td><?= ($userGroup['name']) ?></td>
                    <td class="text-right">
                        <a href="<?= current_url() . "/update/{$userGroup['id']}" ?>">
                        <i class="bi bi-pencil" style="font-size: 20px;"></i>
                        </a>
                        <a href="<?= current_url() . "/delete/{$userGroup['id']}" ?>">
                            <i class="bi bi-trash" style="font-size: 20px;"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
