<style>
input:invalid {
    border-color: #ae0000;
    border-width: 2px;
}
</style>
<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <form method='post' action="<?=esc($editUrl)?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='name'><?=esc($nameLabel)?></label>
                <input class="form-control" id='name' value='<?=esc($name)?>' name='name' required>
                <span class="text-danger"><?= isset($errors['name']) ? $errors['name']: ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <label for='surname'><?=esc($surnameLabel)?></label>
                <input class="form-control" id='surname' value='<?=esc($surname)?>' name='surname' required>
                <span class="text-danger"><?= isset($errors['surname']) ? $errors['surname']: ''; ?></span>
            </div>
        </div>

        <?php if (isset($id)): ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='username'><?= lang('user_lang.field_username') ?></label>
                <input class="form-control" id='username' value='<?= isset($username) ? esc($username) : '' ?>' name='username'>
                <span class="text-danger"><?= isset($errors['surname']) ? $errors['surname']: ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <label for='userType'><?= lang('user_lang.field_usertype') ?></label>
                <?php if ($_SESSION['user_id'] == $id): ?>
                    <div class="alert alert-info"><?= lang('user_lang.user_update_usertype_himself') ?></div>
                <?php endif ?>
                <select <?= $_SESSION['user_id'] == $id ? 'disabled' : null ?> class="form-control" name="fk_user_type" id="userType">
                    <?php foreach($userTypes as $userType): ?>
                        <option value="<?= $userType['id'] ?>" <?= $userType['id'] === $fk_user_type ? 'selected' : null ?>><?= esc($userType['name']) ?></option>
                    <?php endforeach ?>
                </select>
                <span class="text-danger"><?= isset($errors['fk_user_type']) ? $errors['fk_user_type']: ''; ?></span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='email'><?= lang('user_lang.field_email') ?></label>
                <input class="form-control" id='email' name='email' value='<?= isset($email) ? esc($email) : '' ?>'>
                <span class="text-danger"><?= isset($errors['email']) ? $errors['email']: ''; ?></span>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='password'><?= lang('user_lang.field_password') ?></label>
                <input class="form-control" type="password" id='password' name='password'>
                <span class="text-danger"><?= isset($errors['password']) ? $errors['password']: ''; ?></span>
            </div>
            <div class="form-group col-md-6">
                <label for='user_password_again'><?= lang('user_lang.field_password_confirm') ?></label>
                <input class="form-control" type="password" id='user_password_again' name='user_password_again'>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                <?= lang('tim_lang.siteAccountNotLinked') ?>
            </div>
        <?php endif ?>


        <label for='badgeId'><?=esc($badgeIdLabel)?></label>

        <div class="input-group">
            <div class="input-group-prepend">
                <button type='button' id='delete_badge_id_text' class="btn input-group-text" ><?=esc($eraseLabel)?></button>
            </div>


            <input id='badgeId'  class="form-control"  list='badgeId_list' disabled autocomplete="off" value='<?=esc($badgeId)?>' pattern="^\d*$">
                <datalist id='badgeId_list'>
                    <?php foreach ($availableBadges as $badge):?>
                        <option value='<?=esc($badge)?>'>
                    <?php endforeach?>
                </datalist>
        </div>
        <br>
        <p><a href='<?=esc($siteAccountUrl)?>'><?=esc($siteAccountLabel)?></a></p>
        <div class="form-group text-right">
            <a href='<?=esc($returnUrl)?>' class="btn btn-secondary"><?=esc($backLabel)?></a>
            <input type='submit' value='<?=esc($modifyLabel)?>' class="btn btn-primary">
        </div>
        <a href='<?=esc($deleteUrl)?>' class="btn btn-danger"><?=esc($deleteLabel)?></a>
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
