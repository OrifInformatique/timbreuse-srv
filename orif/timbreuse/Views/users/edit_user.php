<style>
input:invalid {
    border-color: #ae0000;
    border-width: 2px;
}
</style>
<section class="container">
    <h3><?= esc(ucfirst(lang('user_lang.title_user_update'))) ?></h3>
    <form method='post' action="<?= '../edit_user/' . $id_user ?>">
        <?= csrf_field() ?>

        <!-- User Sync (timbreuse) -->
        <div class="form-row">
            <div class="form-group col-md-6">
                <?= form_label(esc(ucfirst(lang('tim_lang.name'))), 'name')
                    .form_input('name', esc($name), ['class' => 'form-control', 'id' => 'name']) ?>
                <span class="text-danger"><?= isset($errors['name']) ? esc($errors['name']) : ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <?= form_label(esc(ucfirst(lang('tim_lang.surname'))), 'surname')
                    .form_input('surname', esc($surname), ['class' => 'form-control', 'id' => 'surname']) ?>
                <span class="text-danger"><?= isset($errors['surname']) ? esc($errors['surname']) : ''; ?></span>
            </div>
        </div>

        <?php if (!isset($id)): ?>
            <div class="alert alert-info" role="alert">
                <?= esc(lang('tim_lang.siteAccountNotLinked')) ?><br>
                <?= esc(lang('tim_lang.fillFieldsToCreateAccount')) ?>
            </div>
        <?php endif ?>

        <!-- User (website) -->
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= form_label(esc(lang('user_lang.field_username')), 'username')
                        .form_input('username', isset($username) ? esc($username) : set_value('username'), ['class' => 'form-control' , 'id' => 'username']) ?>
                    <span class="text-danger"><?= isset($errors['username']) ? esc($errors['username']) : ''; ?></span>
                </div>
                <div class="form-group">
                    <?= form_label(esc(lang('user_lang.field_email')), 'email')
                        .form_input('email', isset($email) ? esc($email) : set_value('email'), ['class' => 'form-control', 'id' => 'email'], 'email') ?>
                    <span class="text-danger"><?= isset($errors['email']) ? esc($errors['email']) : ''; ?></span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <?= form_label(esc(lang('user_lang.field_usertype')), 'fk_user_type') ?>
                <?php if ($_SESSION['user_id'] == $id): ?>
                    <div class="alert alert-info" style="margin-bottom: .71rem;"><?= esc(lang('user_lang.user_update_usertype_himself')) ?></div>
                <?php endif ?>
                <?= form_dropdown('fk_user_type', $userTypes, $fk_user_type, [
                        'class' => 'form-control',
                        'id' => 'fk_user_type',
                        $_SESSION['user_id'] != $id ?: 'disabled' => '',
                    ]) 
                ?>
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
    
                <?= form_input('badgeId', esc($badgeId), [
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
        <?php if ($archive || $date_delete): ?>
            <div class="pb-3">
                <a href='<?= '../reactivate_user/'.$id_user ?>'><?= esc(lang('user_lang.user_reactivate')) ?></a>
            </div>
        <?php endif ?>

        <div class="d-flex mb-3">
            <a href='<?= '../delete_tim_user/' . $id_user ?>' class="btn btn-danger mr-auto"><?= esc(ucfirst(lang('tim_lang.delete'))) ?></a>
            <a href='<?= base_url('Users') ?>' class="btn btn-secondary mr-1"><?= esc(lang('common_lang.btn_cancel')) ?></a>
            <input type='submit' value='<?= esc(lang('common_lang.btn_save')) ?>' class="btn btn-primary">
        </div>

        <input type="hidden" name="timUserId" value="<?= esc($id_user) ?>"/>
        <input type="hidden" name="userId" value="<?= esc($id) ?>"/>
        <input id='hiddenBadgeId' type="hidden" name="badgeId" value="<?= esc($badgeId) ?>"/>
    </form>
    
</section>
<script>
let input = document.getElementById("badgeId");
let button = document.getElementById("delete_badge_id_text");
let hiddenInput = document.getElementById("hiddenBadgeId");

button.onclick = function() {
    input.disabled = false;
    input.value = '';
    hiddenInput.value = '';
    input.focus();
};

input.onchange = function() {
    hiddenInput.value = input.value;
};

</script>
