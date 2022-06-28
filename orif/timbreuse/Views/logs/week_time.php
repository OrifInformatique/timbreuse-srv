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
                        <?= ucfirst(lang('tim_lang.hourly')) ?>
                    </th>
                    <?php foreach ($items as $key => $item) : ?>
                        <th><?= ucfirst(lang('tim_lang.' . $key)) . ' ' . $item['dayNb']  ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <?php foreach ($rows as $halfKey => $row) : ?>
                <tr>
                    <th><?= $row ?></th>
                    <?php foreach ($items as $item) : ?>
                        <td>
                            <p><?= lang('tim_lang.time') . ' : ' . $item[$halfKey]['time'] ?></p>
                            <p><?= lang('tim_lang.firstEntry') . ' : ' . $item[$halfKey]['first'] ?></p>
                            <p><?= lang('tim_lang.lastOuting') . ' : ' . $item[$halfKey]['last'] ?></p>

                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>