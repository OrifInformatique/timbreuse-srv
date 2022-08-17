<div class="items_list container">
    <div class="row mb-2">
        <div class="col-sm-8 text-left">
            <!-- Display list title if defined defined -->
            <?= isset($list_title) ? '<h3>' . esc($list_title) . '</h3>' : '' ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <?php foreach ($items as $key => $item) : ?>
                <tr>
                    <td><?= $labels[$key] ?></td>
                    <td><?= $item ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <a href='../delete_modify_log/<?= $fakeLogId ?>' class="btn btn-danger"><?= ucfirst(lang('tim_lang.delete')) ?></a>
</div>