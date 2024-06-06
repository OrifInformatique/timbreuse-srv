<div class="container">
    <?= form_open(current_url()) ?>
    <?php require 'create_series.php' ?>
    <div class="row mb-3 mt-3">
        <div class="col text-right">
            <a class="btn btn-secondary" href="<?= $route; ?>">
                <?= lang('common_lang.btn_cancel'); ?>
            </a>
            <?= form_submit('save', lang('common_lang.btn_save'), ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?= form_close() ?>
</div>