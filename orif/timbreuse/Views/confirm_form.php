<div class="container">
    <?php if (! empty(service('validation')->getErrors())) : ?>
        <div class="alert alert-danger" role="alert">
            <?= service('validation')->listErrors() ?>
        </div>
    <?php endif ?>
    <p><?= esc($text) ?></p>
    <form action="<?= esc($link) ?>" method="post">
        <?= csrf_field() ?>
        <!-- CSRF protection -->
        <input type="submit" value="<?= esc(ucfirst($label_button)) ?>" class="btn btn-primary">
        <a href="<?= esc($cancel_link) ?>"><input type="button" value="<?= esc(ucfirst(lang("tim_lang.cancel"))) ?>" class="btn btn-secondary"></a>
        <?php foreach ($ids as $nameId => $id): ?>
            <input type="hidden" name="<?= esc($nameId) ?>" value="<?= esc($id) ?>">
        <?php endforeach ?>
    </form>

</div>
