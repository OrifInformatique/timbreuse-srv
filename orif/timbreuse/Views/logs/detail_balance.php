<tr>
    <th class='border-top'><?= esc(ucfirst(lang('tim_lang.workTime'))) ?></th>
    <td colspan="1" class='border-top'> <?= esc($sumWorkTime) ?></td>
</tr>
<tr  >
    <th class='border-bottom'><?= esc(ucfirst(lang('tim_lang.offeredTime'))) ?></th>
    <td colspan="1" class='border-bottom'><?=$offeredTime !== '–' ? '+': ''?><?= esc($offeredTime) ?></td>
</tr>
<tr>
    <th><?= esc(ucfirst(lang('tim_lang.timeTotal'))) ?></th>
    <td colspan="1"><?= esc($sumTime) ?></td>
</tr>
<tr>
    <th colspan="1"><?= esc(ucfirst(lang('tim_lang.dueTime'))) ?></th>
    <td colspan="1" class='border-bottom'><?=$offeredTime !== '–' ? '-': ''?><?= esc($dueTime) ?></td>
</tr>
<tr>
    <th><?= esc(ucfirst(lang('tim_lang.balance'))) ?></th>
    <td colspan="1" class="text-<?= $balance[0] === '+' ? 'success': 'danger font-weight-bold'?>"><?= esc($balance) ?></td>
</tr>
