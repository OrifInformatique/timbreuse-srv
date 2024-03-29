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
    <form method='post' action="<?=esc($postUrl)?>">
        <?= csrf_field() ?>
        <p class="form-row">
            <!-- <div class="form-group"> -->
            <label for='timUserId'><?=esc($labels['user'])?></label>
            <div class="input-group">

                <div class="input-group-prepend">
                    <button type='button' id='delete_user_id_text' class="btn input-group-text" ><?=esc($labels['erase'])?></button>
                    <input type="text" id="namesUser" disabled class="input-group-text" value="<?=esc($availableUsers[0]["name"])?> <?=esc($availableUsers[0]["surname"])?>">
                </div>
                <input id='timUserId'  class="form-control" list='userId_list' disabled autocomplete="off" value='<?=esc($availableUsers[0]['id_user'])?>' pattern="^\d*$">
                <datalist id='userId_list'>
                    <?php foreach ($availableUsers as $user):?>
                        <option value="<?=esc($user["id_user"])?>" label="<?=esc($user["name"])?> <?=esc($user["surname"])?>">
                    <?php endforeach?>
                </datalist>
            </div>
        </p>


        <div class="form-group text-right">
            <a href='<?=esc($returnUrl)?>' class="btn btn-secondary"><?=esc($labels['back'])?></a>
            <input type='submit' value='<?=esc($labels['modify'])?>' class="btn btn-primary">
        </div>
        <a href='<?=esc($deleteUrl)?>' class="btn btn-danger"><?=esc($labels['delete'])?></a>
        <input type="hidden" name="badgeId" value="<?=esc($badgeId)?>"/>
        <input id="hiddenUserId" type="hidden" name="timUserId" value="<?=esc($availableUsers[0]['id_user'])?>"/>
    </form>
<script>
let input = document.getElementById("timUserId");
let button = document.getElementById("delete_user_id_text");
let namesbutton = document.getElementById("namesUser");
let userList = document.getElementById("userId_list");
let hiddenInput = document.getElementById("hiddenUserId");

button.onclick = function() {
    input.disabled = false;
    namesbutton.value = "";
    input.value = '';
    hiddenInput.value = '';
    input.focus();
};

input.onchange = function() {
    let isFind = false;
    for (i = 0; i < userList.options.length; i++)
    {
        let userLabel = userList.options[i].label;
        let userValue = userList.options[i].value;
        if (userValue === input.value) {
            namesbutton.value = userLabel;
            //namesbutton.disabled = false;
            isFind = true;
            break;
        }
    }
    if (!isFind) {
        namesbutton.value = "";
        // input.value = "";
    }
    hiddenInput.value = input.value;
};

</script>
