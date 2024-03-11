<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div>
                    <h1><?= lang('tim_lang.delete_user_group', ['group_name' => $userGroup['name']]) ?></h1>
                    <h4><?= lang('user_lang.what_to_do') ?></h4>
                    <?php if (!$canBeDeleted): ?>
                        <p class="alert alert-warning"><?= lang('tim_lang.cannot_delete_group_has_linked') ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <a href="<?= base_url('admin/user-groups'); ?>" class="btn btn-secondary">
                        <?= lang('common_lang.btn_cancel'); ?>
                    </a>
                    <?php if ($canBeDeleted): ?>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteEventType">
                            <?= lang('tim_lang.btn_hard_delete_user_group'); ?>
                        </button>

                        <!-- MODAL DELETE CONFIRMATION -->
                        <div class="modal fade" id="confirmDeleteEventType" tabindex="-1" aria-labelledby="lblConfirmDeleteEventType" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-center">
                                    <div class="modal-header">
                                        <h3 class="modal-title"><?= lang('tim_lang.really_want_to_delete_event_planning') ?></h3>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <?= lang('common_lang.btn_cancel'); ?>
                                        </button>
                                        <form method="post" action="<?= base_url(uri_string() . '/2'); ?>">
                                            <button type="submit" name="confirmation" class="btn btn-danger">
                                                <?= lang('tim_lang.btn_hard_delete_user_group'); ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>