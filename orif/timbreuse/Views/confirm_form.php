<div class="container">

    <p><?= $text ?></p>
    <form action=<?= $link ?> method="post">
        <?= csrf_field() ?>
        <!-- CSRF protection -->
        <input type="submit" value="<?= ucfirst($label_button) ?>" class="btn btn-primary">
        <a href="<?= $cancel_link ?>"><input type="button" value="<?= ucfirst(lang("tim_lang.cancel")) ?>" class="btn btn-link"></a>
        <input type="hidden" name="userId" value="<?= $userId ?>">
        <input type="hidden" name="ciUserId" value="<?= $ciUserId ?>">
    </form>

</div>