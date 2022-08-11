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
                            <div class="font-weight-light">
                                <?= lang('tim_lang.modify') ?>
                            </div>
                        </td>
                    <?php else : ?>
                        <td><?= $item['date'] ?></td>
                    <?php endif; ?>
                    <td><?= $item['time'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <button class="btn btn-primary" ><?= ucfirst(lang('tim_lang.new_record')) ?></button>
</div>
<div class="container mt-5" hidden id='form'>
    <h4>
        <?= ucfirst(lang('tim_lang.new_record')) ?>
    </h4>
    <?= service('validation')->listErrors() ?>
    <form action='../../../../persologs/create_fake_log' method='post'>
        <? csrf_field() ?>
        <div class="form-group">
            <label for="time" class='form-label'><?= ucfirst(lang('tim_lang.hour')) ?></label>
            <input type="time" id='time' class='form-control' name='time'>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" id='in' class='form-check-input' name='inside' value='true'>
                <label for="in" class='form-check-label'>
                    <?= ucfirst(lang('tim_lang.enter')) ?>
                </label>
            </div>
            <div class="form-check">
                <input type="radio" id='out' class='form-check-input' name='inside' value='false'>
                <label for="out" class='form-check-label'>
                    <?= ucfirst(lang('tim_lang.exit')) ?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value=<?= ucfirst(lang('tim_lang.record')) ?>
                >
        </div>
    </form>
</div>
<script>
    let form = document.getElementById("form");
    let button = document.getElementsByTagName("button")[0];
    button.onclick = function () {
        form.hidden = false;
        button.hidden = true;
    }
</script>