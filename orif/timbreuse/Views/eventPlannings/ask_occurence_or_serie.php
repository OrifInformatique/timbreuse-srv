<div class="container">
    <h3><?= lang('tim_lang.event_part_of_serie', $titleParameters) ?></h3>
    <h4><?= lang('tim_lang.modify_or_delete_occurrence_or_serie', $questionParameter) ?></h4>
    <!-- FORM BUTTONS -->
    <?= form_open(current_url()) ?>
    <div class="row mb-3 mt-3">
        <div class="col text-right">
            <a class="btn btn-secondary" href="<?= $returnRoute; ?>"><?= lang('common_lang.btn_cancel'); ?></a>
            <?= form_submit($btnOccurrence, lang("tim_lang.$btnOccurrence"), ['class' => 'btn btn-primary']); ?>
            <?= form_submit($btnSerie, lang("tim_lang.$btnSerie"), ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>