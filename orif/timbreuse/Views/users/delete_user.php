<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $id): ?>
                    <div>
                        <h1><?= lang('user_lang.user').' "'.esc($name).' '.esc($surname).'"' ?></h1>
                        <h4><?= lang('user_lang.what_to_do')?></h4>
                        <div class = "alert alert-info" ><?= lang('user_lang.user_delete_explanation')?></div>
                        <?php if ($archive || $date_delete): ?>
                            <div class = "alert alert-warning" ><?= lang('user_lang.user_allready_disabled')?></div>
                        <?php endif ?>
                    </div>
                    <div class="text-right">
                        <a href="<?= base_url('Users'); ?>" class="btn btn-secondary">
                            <?= lang('common_lang.btn_cancel'); ?>
                        </a>
                        <?php if (!$archive && !$date_delete): ?>
                            <a href="<?= base_url(uri_string().'/1'); ?>" class="btn btn-primary">
                                <?= lang('user_lang.btn_disable_user'); ?>
                            </a>
                        <?php endif ?>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteUser">
                            <?= lang('user_lang.btn_hard_delete_user'); ?>
                        </button>

                        <!-- MODAL DELETE CONFIRMATION -->
                        <div class="modal fade" id="confirmDeleteUser" tabindex="-1" aria-labelledby="lblConfirmDeleteUser" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-center">
                                    <div class="modal-header">
                                        <h3 class="modal-title"><?= lang('tim_lang.really_want_to_delete') ?></h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger"><?= lang('tim_lang.hard_delete_explanation'); ?></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <?= lang('common_lang.btn_cancel'); ?>
                                        </button>
                                        <form method="post" action="<?= base_url(uri_string().'/2'); ?>">
                                            <button type="submit" name="confirmation" class="btn btn-danger">
                                                <?= lang('user_lang.btn_hard_delete_user'); ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div>
                        <h1><?= lang('user_lang.user').' "'.esc($name).' '.esc($surname).'"' ?></h1>
                        <div class = "alert alert-danger" ><?= lang('user_lang.user_delete_himself')?></div>
                    </div>
                    <div class="text-right">
                        <a href="<?= base_url('Users'); ?>" class="btn btn-secondary">
                            <?= lang('common_lang.btn_back'); ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
