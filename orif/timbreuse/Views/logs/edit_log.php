<style>
input:invalid {
    border-color: #ae0000;
    border-width: 2px;
}
</style>
<div class="container mt-5" id='form'>
    <h1>
        <?= esc(ucfirst(lang('tim_lang.recordModification'))) ?>
    </h1>
    <?php if (! empty(service('validation')->getErrors())) : ?>
        <div class="alert alert-danger" role="alert">
            <?= service('validation')->listErrors() ?>
        </div>
    <?php endif ?>
    <form action='<?= esc($update_link) ?>' method='post'>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="time" class='form-label'><?= esc(ucfirst(lang('tim_lang.hour'))) ?></label>
            <input type="time" id='time' class='form-control' name='time' step='1' value='<?= esc(old('time', $time)) ?>' required >
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" id='in' class='form-check-input' name='inside' value='true' <?= esc(old('inside', $inside)) == '1' ? 'checked' : '' ?> required>
                <label for="in" class='form-check-label'>
                    <?= esc(ucfirst(lang('tim_lang.enter'))) ?>
                </label>
            </div>
            <div class="form-check">
                <input type="radio" id='out' class='form-check-input' name='inside' value='false' <?= esc(old('inside', $inside)) == '0' ? 'checked' : '' ?> required>
                <label for="out" class='form-check-label'>
                    <?= esc(ucfirst(lang('tim_lang.exit'))) ?>
                </label>
            </div>
        </div>
        <div class="form-group text-right">
            <a href="<?= esc($cancel_link) ?>"><input type="button" value="<?= esc(ucfirst(lang("tim_lang.cancel"))) ?>" class="btn btn-link"></a>
            <input type="submit" class="btn btn-primary" value=<?= esc(ucfirst(lang('tim_lang.modify'))) ?>>
        </div>
        <div class="form-group">
            <?php if (! $date_delete): ?>
                <a href='<?= esc($delete_link) ?>' class="btn btn-danger"><?= esc(ucfirst(lang('tim_lang.delete'))) ?></a>
            <?php else: ?>
                <a href='<?= esc($restore_link) ?>' class="btn btn-danger"><?= esc(ucfirst(lang('tim_lang.restore'))) ?></a>
            <?php endif ?>
        </div>
        <input type='number' hidden value='<?= esc($id_log) ?>' name='logId'>
    </form>
</div>
