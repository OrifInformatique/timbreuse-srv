<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div>
                    <h1><?= lang('tim_lang.delete_event_type') . ' "' . esc($name) . '"' ?></h1>
                    <h4><?= lang('user_lang.what_to_do') ?></h4>
                </div>
                <div class="text-right">
                    <a href="<?= base_url('admin/event-types'); ?>" class="btn btn-secondary">
                        <?= lang('common_lang.btn_cancel'); ?>
                    </a>
                    <button class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteEventType">
                        <?= lang('tim_lang.btn_hard_delete_event_type'); ?>
                    </button>

                    <!-- MODAL DELETE CONFIRMATION -->
                    <div class="modal fade" id="confirmDeleteEventType" tabindex="-1" aria-labelledby="lblConfirmDeleteEventType" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content text-center">
                                <div class="modal-header">
                                    <h3 class="modal-title"><?= lang('tim_lang.really_want_to_delete_event_type') ?></h3>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <?= lang('common_lang.btn_cancel'); ?>
                                    </button>
                                    <form method="post" action="<?= base_url(uri_string() . '/2'); ?>">
                                        <button type="submit" name="confirmation" class="btn btn-danger">
                                            <?= lang('tim_lang.btn_hard_delete_event_type'); ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>