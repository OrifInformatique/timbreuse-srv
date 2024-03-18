<div class="container">
    <h3><?= lang('tim_lang.event_part_of_serie', $titleParameters) ?></h3>
    <h4><?= lang('tim_lang.modify_occurrence_or_serie') ?></h4>
    <!-- FORM BUTTONS -->
    <?= form_open(current_url()) ?>
    <div class="row mb-3 mt-3">
        <div class="col text-right">
            <a class="btn btn-secondary" href="<?= base_url('admin/event-plannings'); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
            <?= form_submit('modify_occurrence', lang('tim_lang.modify_occurrence'), ['class' => 'btn btn-primary']); ?>
            <?= form_submit('modify_serie', lang('tim_lang.modify_serie'), ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>