<div class="container">
    <h3><?= esc(ucfirst(lang('user_lang.title_user_new'))) ?></h3>
    <form method='post' action="<?= base_url('Users/create_user') ?>">
        <?= csrf_field() ?>

        <!-- User Sync (timbreuse) -->
        <div class="form-row">
            <div class="form-group col-md-6">
                <?= form_label(esc(ucfirst(lang('tim_lang.name'))), 'name')
                    .form_input('name', set_value('name'), ['class' => 'form-control', 'id' => 'name']) ?>
                <span class="text-danger"><?= isset($errors['name']) ? esc($errors['name']) : ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <?= form_label(esc(ucfirst(lang('tim_lang.surname'))), 'surname')
                    .form_input('surname', set_value('surname'), ['class' => 'form-control', 'id' => 'surname']) ?>
                <span class="text-danger"><?= isset($errors['surname']) ? esc($errors['surname']) : ''; ?></span>
            </div>
        </div>
        
        <!-- User (website) -->
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= form_label(esc(lang('user_lang.field_username')), 'username')
                        .form_input('username', set_value('username'), ['class' => 'form-control' , 'id' => 'username']) ?>
                    <span class="text-danger"><?= isset($errors['username']) ? esc($errors['username']) : ''; ?></span>
                </div>
                <div class="form-group">
                    <?= form_label(esc(lang('user_lang.field_email')), 'email')
                        .form_input('email', set_value('email'), ['class' => 'form-control', 'id' => 'email'], 'email') ?>
                    <span class="text-danger"><?= isset($errors['email']) ? esc($errors['email']) : ''; ?></span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <?= form_label(esc(lang('user_lang.field_usertype')), 'fk_user_type')
                    .form_dropdown('fk_user_type', $userTypes, set_value('fk_user_type'), [
                        'class' => 'form-control', 'id' => 'fk_user_type']) ?>
                <span class="text-danger"><?= isset($errors['fk_user_type']) ? esc($errors['fk_user_type']) : ''; ?></span>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <?= form_label(esc(lang('user_lang.field_password')), 'password')
                    .form_input('password', '', ['class' => 'form-control', 'id' => 'password'], 'password') ?>
                <span class="text-danger"><?= isset($errors['password']) ? esc($errors['password']) : ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <?= form_label(esc(lang('user_lang.field_password_confirm')), 'password_confirm')
                    .form_input('password_confirm', '', ['class' => 'form-control', 'id' => 'password_confirm'], 'password') ?>
            </div>
        </div>

        <!-- Badge -->
        <div class="form-group">
            <?= form_label(esc(ucfirst(lang('tim_lang.badgeId'))), 'badgeId') ?>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type='button' id='delete_badge_id_text' class="btn input-group-text" ><?= esc(ucfirst(lang('tim_lang.erase'))) ?></button>
                </div>
    
                <?= form_input('badgeId', set_value('badgeId'), [
                    'id' => 'badgeId',
                    'class' => 'form-control',
                    'list' => 'badgeId_list',
                    'disabled' => 'disabled',
                    'autocomplete' => 'off',
                    'pattern' => '^\d*$'
                ]) ?>
            </div>
            <datalist id='badgeId_list'>
                <?php foreach ($availableBadges as $badge):?>
                    <option value='<?=esc($badge)?>'>
                <?php endforeach?>
            </datalist>
            <span class="text-danger"><?= isset($errors['badgeId']) ? $errors['badgeId'] : ''; ?></span>
        </div>

        <!-- Buttons -->
        <div class="d-flex mb-3 justify-content-end">
            <a href='<?= base_url('Users') ?>' class="btn btn-secondary mr-1"><?= esc(lang('common_lang.btn_cancel')) ?></a>
            <input type='submit' value='<?= esc(lang('common_lang.btn_save')) ?>' class="btn btn-primary">
        </div>
    </form>
</div>

<script>
    let input = document.getElementById("badgeId");
    let button = document.getElementById("delete_badge_id_text");

    button.onclick = function() {
        input.value = '';
        input.disabled = false;
        input.focus();
    };
</script>
