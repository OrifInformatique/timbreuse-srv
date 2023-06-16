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
                    <th>
                        <?= esc(ucfirst(lang('tim_lang.hourly'))) ?>
                    </th>
                    <th></th>
                    <?php foreach ($items as $key => $item) : ?>
                        <th><a href="<?= esc($item['url']) ?>"><?= esc(ucfirst(lang('tim_lang.' . $key)) . ' ' . $item['dayNb'])  ?></a></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php # halfkey is morning afternoon sumtime offeredtime dueTime balance?>
                <?php foreach ($rows as $halfKey => $row) : ?>
                <?php # typeKey is time firstEntry lastOuting ?>
                    <?php foreach ($rows2 as $typeKey => $type) : ?>
                        <?php if (!(in_array($halfKey, $oneRowKey) and ($typeKey != array_key_first($rows2)))) : ?>
                            <tr>
                                <th><?= $typeKey == array_key_first($rows2) ? esc($row) : '' ?></th>
                                <td><?= in_array($halfKey, $oneRowKey) ? '' : esc(ucfirst($type)) ?></td>
                                <?php foreach ($items as $item) : ?>
                                    <td>
                                        <?php if (isset($item[$halfKey][$typeKey])) : ?>
                                            <?php # time or hour  ?>
                                            <?= esc($item[$halfKey][$typeKey]) ?>
                                        <?php elseif (isset($item[$halfKey]) and $halfKey !== 'balance') : ?>
                                            <?php # time sums  ?>
                                            <?= esc($item[$halfKey]) ?>
                                        <?php elseif (isset($item['balance'])) : ?>
                                            <?php # time sums balance ?>
                                            <span class="text-<?= $item['balance'][0] === '+' ? 'success': 'danger font-weight-bold'?>"><?= esc($item['balance']) ?></span>
                                        <?php endif ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <th colspan="6"><?= esc(ucfirst(lang('tim_lang.week'))) ?></th>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.workTime'))) ?></th>
                    <td colspan="6"><?= esc($sumWorkTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.offeredTime'))) ?></th>
                    <td colspan="6"><?= esc($offeredTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.timeTotal'))) ?></th>
                    <td colspan="6"><?= esc($sumTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.dueTime'))) ?></th>
                    <td colspan="6"><?= esc($dueTime) ?></td>
                </tr>
                <tr>
                    <th><?= esc(ucfirst(lang('tim_lang.balance'))) ?></th>
                    <td colspan="6" class="text-<?= $balance[0] == '+' ? 'success': 'danger font-weight-bold'?>"><?= esc($balance) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <p>
        <?= esc(lang('tim_lang.msgAsterisk')) ?>
    </p>
</div>
