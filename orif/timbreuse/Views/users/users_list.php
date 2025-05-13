<?php
/** @author      Orif, section informatique (ViDi, AlKe)
     * @link        https://github.com/OrifInformatique
     * @copyright   Copyright (c), Orif (https://www.orif.ch)
     * 
     * This specific users view is based on a copy of the generic items_list view provided in Common module.
     * It can be used to display a list, create, show details or delete users.
    */

    // If no primary key field name is sent as parameter, suppose it is "id"
    if (!isset($primary_key_field)) {
        $primary_key_field = "id";
    }

    // If no label for create button is sent as parameter, use default label
    if (!isset($btn_create_label)) {
        $btn_create_label = lang('common_lang.btn_add');
    }

    // If no label for display deleted checkbox is sent as parameter, use default label
    if (!isset($display_deleted_label)) {
        $display_deleted_label = lang('common_lang.btn_show_disabled');
    }

    // If no with_deleted variable is sent as parameter, set it to null
    if (!isset($with_deleted)) {
        $with_deleted = null;
    }

    // If no url_getView variable is sent as parameter, set it to null
    if (!isset($url_getView)) {
        $url_getView = null;
    }

    // If no url_restore variable is sent as parameter, set it to null
    if (!isset($url_restore)) {
        $url_restore = null;
    }

    // If no deleted_field variable is sent as parameter, set it to null
    if (!isset($deleted_field)) {
        $deleted_field = null;
    }

    // If no url_duplicate variable is sent as parameter, set it to null
    if (!isset($url_duplicate)) {
        $url_duplicate = null;
    }

    helper('form');
?>

<div class="users_list container">
    <div class="row mb-2">
        <div class="text-left col-12">
            <!-- Display list title if defined defined -->
            <?= isset($list_title) ? '<h3>'.esc($list_title).'</h3>' : '' ?>
        </div>
        <div class="col-sm-6 text-left">
            <!-- Display the "create" button if url_create is defined -->
            <?php if(isset($url_create)): ?>
                <a class="btn btn-primary" href="<?= site_url(esc($url_create)) ?>"><?= esc($btn_create_label) ?></a>
            <?php endif ?>
        </div>
        <div class="col-sm-6 text-right">
            <!-- Display the "with_deleted" checkbox if with_deleted and url_getView variables are defined -->
            <?php if (isset($with_deleted) && isset($url_getView)): ?>
                <label class="form-check-label" for="toggle_deleted">
                    <?= lang($display_deleted_label); ?>
                </label>
                <?= form_checkbox('toggle_deleted', '', $with_deleted, ['id' => 'toggle_deleted']); ?>
            <?php endif ?>
        </div>
    </div>

    <div id="usersList" class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <!-- Get columns headers from the "columns" variable -->
                    <?php foreach ($columns as $column): ?>
                        <th scope="col"><?= $column ?></th>
                    <?php endforeach ?>

                    <!-- Add the "action" column (for detail/update/delete links) -->
                    <?php if(isset($url_detail) || isset($url_update) || isset($url_delete)): ?>
                        <th class="text-right" scope="col"></th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <!-- One table row for each user -->
                <?php foreach ($items as $itemEntity): ?>
                <tr>
                    <!-- Only display user's properties wich are listed in "columns" variable in the order of the columns -->
                    <?php foreach ($columns as $columnKey => $column): ?>
                        <?php if (array_key_exists($columnKey, $itemEntity)) : ?>
                            <?php if (!isset($itemEntity[$deleted_field]) || empty($itemEntity[$deleted_field])) : ?>
                                <td><?= esc($itemEntity[$columnKey]) ?></td>
                            <?php else: ?>
                                <td><del><?= esc($itemEntity[$columnKey]) ?></del></td>
                            <?php endif ?>
                        <?php else: ?>
                            <td></td>
                        <?php endif ?>
                    <?php endforeach ?>

                    <!-- Add the "action" column (for detail/update/delete links) -->
                    <td class="text-right">                        
                        <!-- Bootstrap details icon ("Card text"), redirect to url_detail, adding /primary_key as parameter -->
                        <!-- It's not displayed if the user only exists in user table and is not linked to the user_sync table -->
                        <?php if(isset($url_detail) and isset($itemEntity[$primary_key_field])): ?>
                            <a href="<?= site_url(esc($url_detail.$itemEntity[$primary_key_field])) ?>" class="text-decoration-none" title="<?=lang('common_lang.btn_details') ?>" >
                                <i class="bi bi-card-text" style="font-size: 20px;"></i>
                            </a>
                        <?php endif ?>

                        <!-- Bootstrap edit icon ("Pencil"), redirect to url_update, adding /primary_key as parameter -->
                        <!-- If the user only exists in user table and is not linked to the user_sync table, the link redirects to a specific method with the user table id as parameter -->
                        <?php if(isset($url_update) and isset($itemEntity[$primary_key_field])): ?>
                            <a href="<?= site_url(esc($url_update.$itemEntity[$primary_key_field])) ?>" class="text-decoration-none" title="<?=lang('common_lang.btn_edit') ?>" >
                                <i class="bi bi-pencil" style="font-size: 20px;"></i>
                            </a>
                        <?php elseif(isset($url_update_user) and !isset($itemEntity[$primary_key_field])): ?>
                            <a href="<?= site_url(esc($url_update_user.$itemEntity[$primary_key_field_user])) ?>" class="text-decoration-none" title="<?=lang('common_lang.btn_edit') ?>" >
                                <i class="bi bi-pencil" style="font-size: 20px;"></i>
                            </a>
                        <?php endif ?>
                        
                        <!-- Bootstrap copy icon "files" , redirect to url_duplicate, adding /primary_key as parameter -->
                        <?php if(isset($url_duplicate)): ?>
                            <a href="<?= site_url(esc($url_duplicate.$itemEntity[$primary_key_field])) ?>"
                                    class="text-decoration-none" title="<?=lang('common_lang.btn_copy') ?>" >
                                <i class="bi bi-files" style="font-size: 20px;"></i>
                            </a>
                        <?php endif ?>

                        <!-- Bootstrap delete icon ("Trash"), redirect to url_delete, adding /primary_key as parameter -->
                        <!-- If the user only exists in user table and is not linked to the user_sync table, the link redirects to a specific method with the user table id as parameter -->
                        <?php if ((isset($url_delete)) and (!isset($itemEntity[$deleted_field]) || empty($itemEntity[$deleted_field])) and isset($itemEntity[$primary_key_field])) : ?>
                            <a href="<?= site_url(esc($url_delete.$itemEntity[$primary_key_field])) ?>"
                                    class="text-decoration-none" title="<?=lang('common_lang.btn_delete') ?>" >
                                <i class="bi bi-trash" style="font-size: 20px;"></i>
                            </a>
                        <?php elseif(isset($url_delete_user) and !isset($itemEntity[$primary_key_field])): ?>
                            <a href="<?= site_url(esc($url_delete_user.$itemEntity[$primary_key_field_user])) ?>" class="text-decoration-none" title="<?=lang('common_lang.btn_delete') ?>" >
                                <i class="bi bi-trash" style="font-size: 20px;"></i>
                            </a>
                        <?php endif ?>

                        <!-- Bootstrap restore icon "arrow-counterclockwise", redirect to url_restore,
                                adding/primary_key as parameter -->
                        <?php if ((isset($url_restore)) and (isset($itemEntity[$deleted_field]) &&  !empty($itemEntity[$deleted_field]))) : ?>
                            <a href="<?= site_url(esc($url_restore . $itemEntity[$primary_key_field])) ?>"
                                    class="text-decoration-none" title="<?=lang('common_lang.btn_restore') ?>" >
                                <i class="bi bi-arrow-counterclockwise" style="font-size: 20px;"></i>
                            </a>
                        <?php endif ?>
                        <?php if ((isset($url_delete)) and (isset($itemEntity[$deleted_field]) &&  !empty($itemEntity[$deleted_field]))) : ?>
                            <!-- Bootstrap delete icon ("Trash") with red color, redirect to url_delete, adding /primary_key as parameter -->
                            <a href="<?= site_url(esc($url_delete.$itemEntity[$primary_key_field])) ?>"
                                    class="text-decoration-none" title="<?=lang('common_lang.btn_hard_delete') ?>" >
                                <i class="bi bi-trash text-danger" style="font-size: 20px;"></i>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JQuery script to refresh items list after user action -->
<?php if (isset($url_getView)): ?>
<script>
$(document).ready(function() {

    // "Display disabled items" checkbox value change
    $('#toggle_deleted').change(e => {
        let checked = e.currentTarget.checked;

        // Get view content corresponding to the new parameters and replace current displayed content
        $.post('<?= base_url($url_getView); ?>/'+(+checked), {}, data => {
            $('#usersList').empty();
            $('#usersList')[0].innerHTML = $(data).find('#usersList')[0].innerHTML;
        });
    });
});
</script>
<?php endif; ?>
