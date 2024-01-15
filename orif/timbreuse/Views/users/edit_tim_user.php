<style>
input:invalid {
    border-color: #ae0000;
    border-width: 2px;
}
</style>
<section class="container">
    <h3><?= esc(ucfirst(lang('tim_lang.timUserEdit'))) ?></h3>
    <form method='post' action="<?= '../edit_tim_user/' . $id_user ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='name'><?= esc(ucfirst(lang('tim_lang.name'))) ?></label>
                <input class="form-control" id='name' value='<?=esc($name)?>' name='name' required>
                <span class="text-danger"><?= isset($errors['name']) ? esc($errors['name']) : ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <label for='surname'><?= esc(ucfirst(lang('tim_lang.surname'))) ?></label>
                <input class="form-control" id='surname' value='<?=esc($surname)?>' name='surname' required>
                <span class="text-danger"><?= isset($errors['surname']) ? esc($errors['surname']) : ''; ?></span>
            </div>
        </div>

        <?php if (isset($id)): ?>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for='username'><?= esc(lang('user_lang.field_username')) ?></label>
                    <input class="form-control" id='username' value='<?= isset($username) ? esc($username) : '' ?>' name='username'>
                    <span class="text-danger"><?= isset($errors['username']) ? esc($errors['username']) : ''; ?></span>
                </div>
                <div class="form-group">
                    <label for='email'><?= esc(lang('user_lang.field_email')) ?></label>
                    <input class="form-control" id='email' name='email' value='<?= isset($email) ? esc($email) : '' ?>'>
                    <span class="text-danger"><?= isset($errors['email']) ? esc($errors['email']) : ''; ?></span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for='userType'><?= esc(lang('user_lang.field_usertype')) ?></label>
                <?php if ($_SESSION['user_id'] == $id): ?>
                    <div class="alert alert-info"><?= esc(lang('user_lang.user_update_usertype_himself')) ?></div>
                <?php endif ?>
                <select <?= $_SESSION['user_id'] == $id ? 'disabled' : null ?> class="form-control" name="fk_user_type" id="userType">
                    <?php foreach($userTypes as $userType): ?>
                        <option value="<?= $userType['id'] ?>" <?= $userType['id'] === $fk_user_type ? 'selected' : null ?>><?= esc($userType['name']) ?></option>
                    <?php endforeach ?>
                </select>
                <span class="text-danger"><?= isset($errors['fk_user_type']) ? esc($errors['fk_user_type']) : ''; ?></span>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='password'><?= esc(lang('user_lang.field_password')) ?></label>
                <input class="form-control" type="password" id='password' name='password'>
                <span class="text-danger"><?= isset($errors['password']) ? esc($errors['password']) : ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <label for='user_password_again'><?= esc(lang('user_lang.field_password_confirm')) ?></label>
                <input class="form-control" type="password" id='user_password_again' name='user_password_again'>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                <?= esc(lang('tim_lang.siteAccountNotLinked')) ?>
            </div>
        <?php endif ?>


        <label for='badgeId'><?= esc(ucfirst(lang('tim_lang.badgeId'))) ?></label>

        <div class="input-group">
            <div class="input-group-prepend">
                <button type='button' id='delete_badge_id_text' class="btn input-group-text" ><?= esc(ucfirst(lang('tim_lang.erase'))) ?></button>
            </div>

            <input id='badgeId'  class="form-control"  list='badgeId_list' disabled autocomplete="off" value='<?=esc($badgeId)?>' pattern="^\d*$">
            <datalist id='badgeId_list'>
                <?php foreach ($availableBadges as $badge):?>
                    <option value='<?=esc($badge)?>'>
                <?php endforeach?>
            </datalist>
        </div>
        <span class="text-danger"><?= isset($errors['badgeId']) ? $errors['badgeId'] : ''; ?></span>

        <div class="pt-3 pb-3">
            <a href='<?= '../ci_users_list/'. $id_user ?>'><?= esc(ucfirst(lang('tim_lang.siteAccountLabel'))) ?></a>
        </div>
        
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

        <input type="hidden" name="timUserId" value="<?=esc($id_user)?>"/>
        <input type="hidden" name="userId" value="<?=esc($id)?>"/>
        <input id='hiddenBadgeId' type="hidden" name="badgeId" value="<?=esc($badgeId)?>"/>
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
