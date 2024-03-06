<?php $update = boolval(isset($userGroup)); ?>
<div class="container">
    <!-- TITLE -->
    <div class="row mt-3">
        <div class="col">
            <h1 class="title-section"><?= lang('tim_lang.'.($update ? 'update' : 'create').'_user_group_title'); ?></h1>
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
                    <?= form_label(lang('tim_lang.field_name'), 'name', ['class' => 'form-label']); ?>
                    <?= form_input('name', $sessionUserGroup['name'] ?? $userGroup['name'] ?? set_value('name'), [
                        'class' => 'form-control'
                    ]); ?>
                    <span class="text-danger"><?= isset($errors['name']) ? esc($errors['name']) : ''; ?></span>
                </div>
            </div>
            <div class="col-sm-6">
                <?= form_label(lang('tim_lang.field_group_parent_name'), 'parentGroupName', ['class' => 'form-label']); ?>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type='button' id='deleteParentGroup' class="btn input-group-text" ><?= ucfirst(lang('tim_lang.erase')) ?></button>
                    </div>

                    <?= form_input('parentUserGroupName', $parentUserGroup['name'] ?? '', [
                        'class' => 'form-control',
                        'disabled' => '',
                        'id' => 'parentUserGroupName'
                    ]); ?>
                </div>
                <span class="text-danger w-100"><?= isset($errors['fk_user_group_id']) ? esc($errors['fk_user_group_id']) : ''; ?></span>
                <?= form_submit('selectParentUserGroupButton', lang('tim_lang.select_parent_group'), ['class' => 'mt-3 w-100 btn btn-secondary']); ?>
            </div>
        </div>

        <input type="hidden" name="parentUserGroupId" id="parentUserGroupId" value="<?= $parentUserGroup['id'] ?? '' ?>">
                    
        <!-- FORM BUTTONS -->
        <div class="row mb-3 mt-3">
            <div class="col text-right">
                <a class="btn btn-secondary" href="<?= base_url('admin/user-groups'); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
                <?= form_submit('save', lang('common_lang.btn_save'), ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    <?= form_close(); ?>
</div>

<script>
    let parentUserGroupInput = document.getElementById("parentUserGroupName");
    let hiddenParentUserGroupId = document.getElementById("parentUserGroupId");
    let deleteButton = document.getElementById("deleteParentGroup");

    deleteButton.onclick = () => {
        parentUserGroupInput.value = '';
        hiddenParentUserGroupId.value = '';
    };
</script>
