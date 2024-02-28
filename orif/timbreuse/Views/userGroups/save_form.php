<?php $update = boolval(isset($userGroup)); ?>
<div class="container">
    <!-- TITLE -->
    <div class="row mt-3">
        <div class="col">
            <h1 class="title-section"><?= lang('tim_lang.'.($update ? 'update' : 'create').'UserGroupTitle'); ?></h1>
        </div>
    </div>
    
    <!-- FORM OPEN -->
    <?= form_open('admin/user-groups/'.($update ? "update/{$userGroup['id']}" : 'create'), [], [
            'id' => $userGroup['id'] ?? 0
        ]);
    ?>
        <!-- FORM FIELDS -->
        <div class="row mt-3">
            <div class="col-sm-6">
                <div class="form-group">
                    <?= form_label(lang('tim_lang.fieldName'), 'name', ['class' => 'form-label']); ?>
                    <?= form_input('name', $userGroup['name'] ?? '', [
                        'class' => 'form-control', 'required' => ''
                    ]); ?>
                    <span class="text-danger"><?= isset($errors['name']) ? esc($errors['name']) : ''; ?></span>
                </div>
            </div>
            <div class="col-sm-6 form-group">
                <div class="form-group">
                    <?= form_label(lang('tim_lang.fieldName'), 'parentGroupName', ['class' => 'form-label']); ?>
                    <?= form_input('parentGroupName', $userGroup['fk_user_group_id'] ?? '', [
                        'class' => 'form-control', 'disabled' => ''
                    ]); ?>
                    <span class="text-danger"><?= isset($errors['fk_user_group_id']) ? esc($errors['fk_user_group_id']) : ''; ?></span>
                </div>
            </div>
        </div>
                    
        <!-- FORM BUTTONS -->
        <div class="row">
            <div class="col text-right">
                <a class="btn btn-secondary" href="<?= base_url('admin/user-groups'); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
                <?= form_submit('save', lang('common_lang.btn_save'), ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    <?= form_close(); ?>
</div>
