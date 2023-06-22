<div class="items_list container">
    <div class="row mb-2">
        <div class="col-sm-8 text-left">
            <!-- Display list title if defined defined -->
            <?= isset($list_title) ? '<h3>' . esc($list_title) . '</h3>' : '' ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <?php foreach ($columns as $column) : ?>
                        <th><?= esc(ucfirst($column)) ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item) : ?>
                    <tr>
                        <th><a href="<?= esc($item['url']) ?>"><?= esc($item['label_week']) ?></a></th>
                        <td><?= esc($item['time']) ?></td>
                    </tr>
            <?php endforeach ?>
            </tbody>
            <tfoot class="table table-borderless">
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <th><?= esc(ucfirst(lang('tim_lang.monthDetail'))) ?></th>
                </tr>
                <?=view('Timbreuse\Views\logs\detail_balance', $this->data)?>
            </tfoot>
        </table>
    </div>
</div>
