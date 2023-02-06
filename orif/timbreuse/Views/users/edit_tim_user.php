<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <?= session()->getFlashdata('error') ?>
    <?= service('validation')->listErrors() ?>
    <form method='post' action="<?=$editUrl?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for='name'><?=$nameLabel?></label>
                <input class="form-control" id='name'value='<?=$name?>' name='name'>
            </div>
            <div class="form-group col-md-6">
                <label for='surname'><?=$surnameLabel?></label>
                <input class="form-control" id='surname' value='<?=$surname?>' name='surname'>
            </div>
        </div>
        <p><a href='<?=$siteAccountUrl?>'><?=$siteAccountLabel?></a></p>
        <p><a href='<?=$allocationBadgeUrl?>'><?=$allocationBadgeLabel?></a></p>
        <div class="form-group text-right">
            <a href='<?=$returnUrl?>' class="btn btn-link"><?=$backLabel?></a>
            <input type='submit' value='<?=$modifyLabel?>' class="btn btn-primary">
        </div>
        <a href='<?=$deleteUrl?>' class="btn btn-danger"><?=$deleteLabel?></a>
        <input type="hidden" name="timUserId" value="<?=$id_user?>"/>
    </form>
    
</section>