<!-- detail log modified -->
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
                    <td><?= esc($labels[$key]) ?></td>
                    <td><?= esc($item) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>
