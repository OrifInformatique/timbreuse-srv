<?php $update = boolval(isset($eventType)); ?>
<div class="container">
    <!-- TITLE -->
    <div class="row mt-3">
        <div class="col">
            <h1 class="title-section"><?= lang('tim_lang.' . ($update ? 'update' : 'create') . 'EventTypeTitle'); ?></h1>
        </div>
    </div>

    <!-- FORM OPEN -->
    <?= form_open('admin/event-types/' . ($update ? "update/{$eventType['id']}" : 'create'), [], [
        'id' => $eventType['id'] ?? 0
    ]);
    ?>
    <!-- FORM FIELDS -->
    <div class="row mt-3">
        <div class="col-sm-6">
            <div class="form-group">
                <?= form_label(lang('tim_lang.fieldName'), 'name', ['class' => 'form-label']); ?>
                <?= form_input('name', $eventType['name'] ?? set_value('name'), [
                    'class' => 'form-control', 'required' => ''
                ]); ?>
                <span class="text-danger"><?= isset($errors['name']) ? esc($errors['name']) : ''; ?></span>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <?= form_label(lang('tim_lang.fieldIsGroupEventType'), 'isGroupEventTypeYes', ['class' => 'form-label']); ?>
                <div>
                    <div class="form-check form-check-inline">
                        <?= form_radio(
                            'isGroupEventType',
                            '1',
                            $update ? $eventType['is_group_event_type'] : true,
                            ['class' => 'form-check-input', 'id' => 'isGroupEventTypeYes']
                        ) ?>
                        <?= form_label(lang('common_lang.yes'), 'isGroupEventTypeYes', ['class' => 'form-check-label']); ?>
                    </div>
                    <div class="form-check form-check-inline">
                        <?= form_radio(
                            'isGroupEventType',
                            '0',
                            $update ?  !$eventType['is_group_event_type'] : false,
                            ['class' => 'form-check-input', 'id' => 'isGroupEventTypeNo']
                        ) ?>
                        <?= form_label(lang('common_lang.no'), 'isGroupEventTypeNo', ['class' => 'form-check-label']); ?>
                    </div>
                </div>
                <span class="text-danger"><?= isset($errors['is_group_event_type']) ? esc($errors['is_group_event_type']) : ''; ?></span>
            </div>
            <div class="form-group">
                <?= form_label(lang('tim_lang.fieldIsPersonalEventType'), 'isPersonalEventTypeYes', ['class' => 'form-label']); ?>
                <div>
                    <div class="form-check form-check-inline">
                        <?= form_radio(
                            'isPersonalEventType',
                            '1',
                            $update ? $eventType['is_personal_event_type'] : true,
                            ['class' => 'form-check-input', 'id' => 'isEventTypeYes']
                        ) ?>
                        <?= form_label(lang('common_lang.yes'), 'isEventTypeYes', ['class' => 'form-check-label']); ?>
                    </div>
                    <div class="form-check form-check-inline">
                        <?= form_radio(
                            'isPersonalEventType',
                            '0',
                            $update ? !$eventType['is_personal_event_type'] : false,
                            ['class' => 'form-check-input', 'id' => 'isPersonalEventTypeNo']
                        ) ?>
                        <?= form_label(lang('common_lang.no'), 'isPersonalEventTypeNo', ['class' => 'form-check-label']); ?>
                    </div>
                </div>
                <span class="text-danger"><?= isset($errors['is_personal_event_type']) ? esc($errors['is_personal_event_type']) : ''; ?></span>
            </div>
        </div>
    </div>

    <!-- FORM BUTTONS -->
    <div class="row mb-3 mt-3">
        <div class="col text-right">
            <a class="btn btn-secondary" href="<?= base_url('admin/event-types'); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
            <?= form_submit('save', lang('common_lang.btn_save'), ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>