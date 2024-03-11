<div class="container">
    <ul class="nav nav-tabs nav-fill pt-3">
        <li class="nav-item">
            <a class="nav-link <?= url_is('*event-plannings/personal*') ? 'active' : '' ?>" href="<?= base_url('admin/event-plannings/personal/create') ?>"><?= lang('tim_lang.field_is_personal_event_type') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= url_is('*event-plannings/group*') ? 'active' : '' ?>" href="<?= base_url('admin/event-plannings/group/create') ?>"><?= lang('tim_lang.field_is_group_event_type') ?></a>
        </li>
    </ul>
</div>