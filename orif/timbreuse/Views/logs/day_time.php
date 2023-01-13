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
                        <th><?= ucfirst($column) ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <?php foreach ($items as $item) : ?>
                <tr>
                    <?php if (isset($item['url'])) : ?>
                        <td>
                            <a href="<?= $item['url'] ?>">
                                <?= $item['date'] ?>
                            </a>
                            <span class="badge badge-primary">
                                <?= lang('tim_lang.modified') ?>
                            </span>
                        </td>
                    <?php else : ?>
                        <td><?= $item['date'] ?></td>
                    <?php endif; ?>
                    <!-- enter exit and time in last row -->
                    <td><?= $item['time'] ?></td>
                    <td><a href='<?= '#' ?>'>o</a> </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <button class="btn btn-primary" <?= is_null(old('time')) && is_null(old('inside')) ? '' : 'hidden' ?>>
        <?= ucfirst(lang('tim_lang.new_record')) ?> 
    </button>
</div>
<div class="container mt-5" id='form' <?= is_null(old('time')) && is_null(old('inside')) ? 'hidden' : '' ?>>
    <h4>
        <?= ucfirst(lang('tim_lang.new_record')) ?>
    </h4>
    <?= service('validation')->listErrors() ?>
    <form action='<?=base_url() ?>/PersoLogs/create_modify_log' method='post'>
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="time" class='form-label'><?= ucfirst(lang('tim_lang.hour')) ?></label>
            <input type="time" id='time' class='form-control' name='time' step='1' value='<?= old('time') ?>'>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" id='in' class='form-check-input' name='inside' value='true' <?= old('inside') == 'true' ? 'checked' : '' ?>>
                <label for="in" class='form-check-label'>
                    <?= ucfirst(lang('tim_lang.enter')) ?>
                </label>
            </div>
            <div class="form-check">
                <input type="radio" id='out' class='form-check-input' name='inside' value='false' <?= old('inside') == 'false' ? 'checked' : '' ?>>
                <label for="out" class='form-check-label'>
                    <?= ucfirst(lang('tim_lang.exit')) ?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value=<?= ucfirst(lang('tim_lang.record')) ?>>
        </div>
        <input type="date" hidden value='<?= $date ?>' name='date'>
        <input type="number" hidden value='<?= $userId ?>' name='userId'>
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