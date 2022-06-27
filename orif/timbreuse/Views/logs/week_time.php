<div class="items_list container">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>
                        <?= ucfirst(lang('lang.hourly')) ?>
                    </th>
                    <?php foreach ($items as $key => $item) : ?>
                        <th><?= ucfirst(lang('lang.' . $key)) . ' ' . $item['dayNb']  ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <?php foreach ($rows as $halfKey => $row) : ?>
                <tr>
                    <th><?= $row ?></th>
                    <?php foreach ($items as $item) : ?>
                        <td>
                            <p><?= lang('lang.time') . ' : ' . $item[$halfKey]['time'] ?></p>
                            <p><?= lang('lang.firstEntry') .' : ' . $item[$halfKey]['first'] ?></p>
                            <p><?= lang('lang.lastOuting') .' : ' . $item[$halfKey]['last'] ?></p>

                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>