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
                        <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <?php if (isset($item['status']) and ($item['status'] == 'deleted')) : ?>
                            <td>
                                <del><?= esc($item['date']) ?></del>
                                <span class="badge badge-secondary">
                                    <?= esc(lang('tim_lang.deleted')) ?>
                                </span>
                            </td>
                        <?php elseif (isset($item['url'])) : ?>
                            <td>
                                <a href="<?= esc($item['url']) ?>"><?= esc($item['date']) ?></a>
                                <span class="badge badge-primary">
                                    <?php if ($item['status'] == 'site') : ?>
                                        <?= esc(lang('tim_lang.siteStatus')) ?>
                                    <?php elseif ($item['status'] == 'modified') : ?>
                                        <?= esc(lang('tim_lang.modified')) ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                        <?php else : ?>
                            <td><?= esc($item['date']) ?></td>
                        <?php endif; ?>
                        <?php if (isset($item['status']) and ($item['status'] == 'deleted')) : ?>
                            <td><del><?= esc($item['time']) ?></del></td>
                        <?php else : ?>
                        <!-- enter exit and time in last row -->
                            <td><?= esc($item['time']) ?></td>
                        <?php endif; ?>
                        <?php if (isset($item['edit_url'])) : ?>
                            <td>
                                <a href='<?= esc($item['edit_url']) ?>'>
                                    <i class="bi-pencil" style="font-size: 20px;" title="<?=lang('common_lang.btn_edit')?>" ></i>
                                </a>
                            </td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot class="table table-borderless">
                <?=view('Timbreuse\Views\logs\detail_balance', $this->data)?>
            </tfoot>
        </table>
    </div>
    <button class="btn btn-primary" <?= is_null(old('time')) && is_null(old('inside')) ? '' : 'hidden' ?>>
        <?= esc(ucfirst(lang('tim_lang.new_record'))) ?> 
    </button>
</div>
<div class="container mt-5" id='form' <?= is_null(old('time')) && is_null(old('inside')) ? 'hidden' : '' ?>>
    <h4>
        <?= esc(ucfirst(lang('tim_lang.new_record'))) ?>
    </h4>
    <?php if (! empty(service('validation')->getErrors())) : ?>
        <div class="alert alert-danger" role="alert">
            <?= service('validation')->listErrors() ?>
        </div>
    <?php endif ?>
    <form action='<?=esc(base_url()) ?>/PersoLogs/create_modify_log' method='post'>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="time" class='form-label'><?= esc(ucfirst(lang('tim_lang.hour'))) ?></label>
            <input type="time" id='time' class='form-control' name='time' step='1' value='<?= esc(old('time')) ?>' required>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" id='in' class='form-check-input' name='inside' value='true' <?= esc(old('inside')) == 'true' ? 'checked' : '' ?> required>
                <label for="in" class='form-check-label'>
                    <?= esc(ucfirst(lang('tim_lang.enter'))) ?>
                </label>
            </div>
            <div class="form-check">
                <input type="radio" id='out' class='form-check-input' name='inside' value='false' <?= esc(old('inside')) == 'false' ? 'checked' : '' ?> required>
                <label for="out" class='form-check-label'>
                    <?= esc(ucfirst(lang('tim_lang.exit'))) ?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value=<?= esc(ucfirst(lang('tim_lang.record'))) ?>>
        </div>
        <input type="date" hidden value='<?= esc($date) ?>' name='date'>
        <input type="number" hidden value='<?= esc($userId) ?>' name='userId'>
    </form>
</div>
<script>
    let form = document.getElementById("form");
    let button = document.getElementsByTagName("button")[0];
    button.onclick = function() {
        form.hidden = false;
        button.hidden = true;
    }
</script>
