<div class="container mt-5" id='form'>
    <h1>
        <?= ucfirst(lang('tim_lang.recordModification')) ?>
    </h1>
    <?= service('validation')->listErrors() ?>
    <form action='<?= $update_link ?>' method='post'>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="time" class='form-label'><?= ucfirst(lang('tim_lang.hour')) ?></label>
            <input type="time" id='time' class='form-control' name='time' step='1' value='<?= old('time', $time) ?>'>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" id='in' class='form-check-input' name='inside' value='true' <?= old('inside', $inside) == '1' ? 'checked' : '' ?>>
                <label for="in" class='form-check-label'>
                    <?= ucfirst(lang('tim_lang.enter')) ?>
                </label>
            </div>
            <div class="form-check">
                <input type="radio" id='out' class='form-check-input' name='inside' value='false' <?= old('inside', $inside) == '0' ? 'checked' : '' ?>>
                <label for="out" class='form-check-label'>
                    <?= ucfirst(lang('tim_lang.exit')) ?>
                </label>
            </div>
        </div>
        <div class="form-group text-right">
            <a href="<?= $cancel_link ?>"><input type="button" value="<?= ucfirst(lang("tim_lang.cancel")) ?>" class="btn btn-link"></a>
            <input type="submit" class="btn btn-primary" value=<?= ucfirst(lang('tim_lang.modify')) ?>>
        </div>
        <div class="form-group">
            <?php if (! $date_delete): ?>
                <a href='<?= $delete_link ?>' class="btn btn-danger"><?= ucfirst(lang('tim_lang.delete')) ?></a>
            <?php else: ?>
                <a href='<?= $restore_link ?>' class="btn btn-danger"><?= ucfirst(lang('tim_lang.restore')) ?></a>
            <?php endif ?>
        </div>
        <input type='number' hidden value='<?= $id_log ?>' name='logId'>
    </form>
</div>