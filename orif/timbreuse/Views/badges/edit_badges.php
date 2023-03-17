<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <?= session()->getFlashdata('error') ?>
    <?= service('validation')->listErrors() ?>
    <form method='post' action="<?=$postUrl?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label for='user-select'><?=$labels['user']?></label>
                <select name="userId" id="user-select">
                    <?php foreach ($availableUsers as $user):?>
                        <option value="<?=$user["id_user"]?>"><?=$user["name"]?> <?=$user["surname"]?></option>
                    <?php endforeach?>
                </select>
            </div>
        </div>


        <div class="form-group text-right">
            <a href='<?=$returnUrl?>' class="btn btn-link"><?=$labels['back']?></a>
            <input type='submit' value='<?=$labels['modify']?>' class="btn btn-primary">
        </div>
        <a href='<?=$deallocUrl?>' class="btn btn-danger"><?=$labels['dealloc']?></a>
        <input type="hidden" name="timUserId" value="<?=$badgeId?>"/>
    </form>
