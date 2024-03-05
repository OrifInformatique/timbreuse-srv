<h3><?= lang('tim_lang.btn_create_series') ?></h3>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?= form_label(lang('tim_lang.field_start_date'), 'start_date', ['class' => 'form-label']); ?>
            <?= form_input('start_date', $eventSerie['start_date'] ?? set_value('start_date'), [
                'class' => 'form-control', 'id' => 'start_date'
            ], 'date'); ?>
            <span class="text-danger"><?= isset($errors['start_date']) ? esc($errors['start_date']) : ''; ?></span>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?= form_label(lang('tim_lang.field_end_date'), 'end_date', ['class' => 'form-label']); ?>
            <?= form_input('end_date', $eventSerie['end_date'] ?? set_value('end_date'), [
                'class' => 'form-control', 'id' => 'end_date'
            ], 'date'); ?>
            <span class="text-danger"><?= isset($errors['end_date']) ? esc($errors['end_date']) : ''; ?></span>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?= form_label(lang('tim_lang.field_recurrence_frequency'), 'recurrence_frequency', ['class' => 'form-label']); ?>
            <?= form_input('recurrence_frequency', $eventSerie['recurrence_frequency'] ?? set_value('recurrence_frequency'), [
                'class' => 'form-control', 'id' => 'recurrence_frequency'
            ]); ?>
            <span class="text-danger"><?= isset($errors['recurrence_frequency']) ? esc($errors['recurrence_frequency']) : ''; ?></span>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?= form_label(lang('tim_lang.field_recurrence_interval'), 'recurrence_interval', ['class' => 'form-label']); ?>
            <?= form_input('recurrence_interval', $eventSerie['recurrence_interval'] ?? set_value('recurrence_interval'), [
                'class' => 'form-control', 'id' => 'recurrence_interval', 'min' => '1'
            ], 'number'); ?>
            <span class="text-danger"><?= isset($errors['recurrence_interval']) ? esc($errors['recurrence_interval']) : ''; ?></span>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?= form_label(lang('tim_lang.field_days_of_week'), '', ['class' => 'form-label']); ?>
            <div>
                <?php
                    foreach ($daysOfWeek as $day) :
                ?>
                    <div class="form-check form-check-inline">
                        <?= form_checkbox($day, $day, false, ['id' => $day, 'class' => 'form-check-input']); ?>
                        <?= form_label(ucfirst($day), $day, ['class' => 'form-check-label']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>