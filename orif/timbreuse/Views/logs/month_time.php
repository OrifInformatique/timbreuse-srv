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
            <tfoot>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.monthTime'))) ?></th>
                    <td colspan="2"><?= esc($sumWorkTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.offeredTime'))) ?></th>
                    <td colspan="2"><?= esc($offeredTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.timeTotal'))) ?></th>
                    <td colspan="2"><?= esc($sumTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.dueTime'))) ?></th>
                    <td colspan="2"><?= esc($dueTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.balance'))) ?></th>
                    <td class="text-<?= $balance[0] === '+' ? 'success': 'danger font-weight-bold'?>"><?= esc($balance) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
