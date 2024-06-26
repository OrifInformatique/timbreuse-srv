<?php 
    $update = isset($eventPlanning);
    $eventPlanningId = $eventPlanning['id'] ?? 0;    
?>

<div class="container">
    <!-- TITLE -->
    <div class="row mt-3">
        <div class="col">
            <h1 class="title-section"><?= lang('tim_lang.' . ($update ? 'update' : 'create') . '_personal_event_planning_title'); ?></h1>
        </div>
    </div>

    <!-- FORM OPEN -->
    <?= form_open($formAction, [], [
        'id' => $eventPlanningId
    ]);
    ?>
    <!-- FORM FIELDS -->
    <div class="row mt-3">
        <div class="col-sm-6">
            <div class="form-group">
                <?= form_label(lang('tim_lang.event_type'), 'event_type', ['class' => 'form-label']); ?>
                <?= form_dropdown('fk_event_type_id', $eventTypes, $sessionEventPlanning['fk_event_type_id'] ?? $eventPlanning['fk_event_type_id'] ?? set_value('fk_event_type_id'), [
                    'class' => 'form-control', 'id' => 'event_type'
                ]); ?>
                <span class="text-danger"><?= isset($errors['fk_event_type_id']) ? esc($errors['fk_event_type_id']) : ''; ?></span>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <?= form_label(lang('tim_lang.field_linked_user'), 'linked_user', ['class' => 'form-label']); ?>
                <?= form_input('', !is_null($user) ? "{$user['name']} {$user['surname']}" : '', [
                    'class' => 'form-control', 'id' => 'linked_user', 'disabled' => ''
                ]); ?>
                <span class="text-danger"><?= isset($errors['fk_user_sync_id']) ? esc($errors['fk_user_sync_id']) : ''; ?></span>
                <?php if (url_is('*admin*')) : ?>
                    <?= form_submit('select_linked_user', lang('tim_lang.btn_select_linked_user'), ['class' => 'mt-3 w-100 btn btn-secondary']); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <?= form_label(lang('tim_lang.field_event_date'), 'event_date', ['class' => 'form-label']); ?>
                <?= form_input('event_date', $sessionEventPlanning['event_date'] ?? $eventPlanning['event_date'] ?? set_value('event_date'), [
                    'class' => 'form-control', 'id' => 'event_date',
                ], 'date'); ?>
                <span class="text-danger"><?= isset($errors['event_date']) ? esc($errors['event_date']) : ''; ?></span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <?= form_label(lang('tim_lang.field_start_time'), 'start_time', ['class' => 'form-label']); ?>
                <?= form_input('start_time', $sessionEventPlanning['start_time'] ?? $eventPlanning['start_time'] ?? set_value('start_time'), [
                    'class' => 'form-control', 'id' => 'start_time',
                ], 'time'); ?>
                <span class="text-danger"><?= isset($errors['start_time']) ? esc($errors['start_time']) : ''; ?></span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <?= form_label(lang('tim_lang.field_end_time'), 'end_time', ['class' => 'form-label']); ?>
                <?= form_input('end_time', $sessionEventPlanning['end_time'] ?? $eventPlanning['end_time'] ?? set_value('end_time'), [
                    'class' => 'form-control', 'id' => 'end_time',
                ], 'time'); ?>
                <span class="text-danger"><?= isset($errors['end_time']) ? esc($errors['end_time']) : ''; ?></span>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?= form_label(lang('tim_lang.field_is_work_time'), 'is_work_time_yes', ['class' => 'form-label']); ?>
                <div>
                    <div class="form-check form-check-inline">
                        <?= form_radio(
                            'is_work_time',
                            '1',
                            $update ? 
                                $eventPlanning['is_work_time'] : (!is_null($sessionEventPlanning) ? 
                                    $sessionEventPlanning['is_work_time'] : 
                                    true),
                            ['class' => 'form-check-input', 'id' => 'is_work_time_yes']
                        ) ?>
                        <?= form_label(lang('common_lang.yes'), 'is_work_time_yes', ['class' => 'form-check-label']); ?>
                    </div>
                    <div class="form-check form-check-inline">
                        <?= form_radio(
                            'is_work_time',
                            '0',
                            $update ?
                                !$eventPlanning['is_work_time'] : (!is_null($sessionEventPlanning) ?
                                    !$sessionEventPlanning['is_work_time'] :
                                    false),
                            ['class' => 'form-check-input', 'id' => 'is_work_time_no']
                        ) ?>
                        <?= form_label(lang('common_lang.no'), 'is_work_time_no', ['class' => 'form-check-label']); ?>
                    </div>
                </div>
                <span class="text-danger"><?= isset($errors['is_work_time']) ? esc($errors['is_work_time']) : ''; ?></span>
            </div>
        </div>
    </div>

    <?= form_input('linked_user_id', $user['id_user'] ?? '', ['hidden' => '']) ?>
    
    <?php if (!$update): ?>
        <?= form_button('', lang('tim_lang.btn_create_series'), ['class' => 'btn btn-primary', 'id' => 'create_series']) ?>
        <div id="create_series_form"></div>
    <?php endif; ?>

    <!-- FORM BUTTONS -->
    <div class="row mb-3 mt-3">
        <div class="col text-right">
            <a class="btn btn-secondary" href="<?= $route; ?>"><?= lang('common_lang.btn_cancel'); ?></a>
            <?= form_submit('save', lang('common_lang.btn_save'), ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>
