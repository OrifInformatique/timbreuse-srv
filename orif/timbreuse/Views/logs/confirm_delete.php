<div class="container">

    <p><?= $text ?></p>
    <form action=<?= $link ?> method="post">
        <?= csrf_field() ?>
        <!-- CSRF protection -->
        <input type="submit" value="<?= ucfirst($label_button) ?>" class="btn btn-danger">
        <a href="<?= $cancel_link ?>"><input type="button" value="<?= ucfirst(lang("tim_lang.cancel")) ?>" class="btn btn-link"></a>
        <input type="hidden" name="id" value="<?= $id ?>">
    </form>

</div>