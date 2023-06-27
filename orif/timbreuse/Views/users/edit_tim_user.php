<style>
input:invalid {
    border-color: #ae0000;
    border-width: 2px;
}
</style>
<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <!-- <?= session()->getFlashdata('error') ?> -->
    <?php if (! empty(service('validation')->getErrors())) : ?>
        <div class="alert alert-danger" role="alert">
            <?= service('validation')->listErrors() ?>
        </div>
    <?php endif ?>
    <form method='post' action="<?=esc($editUrl)?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='name'><?=esc($nameLabel)?></label>
                <input class="form-control" id='name'value='<?=esc($name)?>' name='name' required>
            </div>
            <div class="form-group col-md-6">
                <label for='surname'><?=esc($surnameLabel)?></label>
                <input class="form-control" id='surname' value='<?=esc($surname)?>' name='surname' required>
            </div>
        </div>


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
