<div class="container">

    <p><?= esc($text) ?></p>
    <form action=<?= esc($link) ?> method="post">
        <?= csrf_field() ?>
        <!-- CSRF protection -->
        <input type="submit" value="<?= esc(ucfirst($label_button)) ?>" class="btn btn-primary">
        <a href="<?= esc($cancel_link) ?>"><input type="button" value="<?= esc(ucfirst(lang("tim_lang.cancel"))) ?>" class="btn btn-link"></a>
        <input type="hidden" name="userId" value="<?= esc($userId) ?>">
        <input type="hidden" name="ciUserId" value="<?= esc($ciUserId) ?>">
    </form>

</div>
