<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <?= session()->getFlashdata('error') ?>
    <?= service('validation')->listErrors() ?>
    <form method='post' action="<?=esc($postUrl)?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label for='user-select'><?=esc($labels['user'])?></label>
                <select class="form-control" name="timUserId" id="user-select">
                    <?php foreach ($availableUsers as $user):?>
                        <option value="<?=esc($user["id_user"])?>"><?=esc($user["name"])?> <?=esc($user["surname"])?></option>
                    <?php endforeach?>
                </select>
            </div>
        </div>


        <div class="form-group text-right">
            <a href='<?=esc($returnUrl)?>' class="btn btn-link"><?=esc($labels['back'])?></a>
            <input type='submit' value='<?=esc($labels['modify'])?>' class="btn btn-primary">
        </div>
<!--        <a href='<?=$deallocUrl?>' class="btn btn-danger"><?=$labels['dealloc']?></a> -->
        <input type="hidden" name="badgeId" value="<?=esc($badgeId)?>"/>
    </form>
