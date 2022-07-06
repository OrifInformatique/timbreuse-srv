<div class="container">

    <p>question</p>
    <form action="/add_access" method="post">
        <?= csrf_field() ?>
        <!-- CSRF protection -->
        <input type="submit" value="<?= lang("tim_lang.delete") ?>" class="btn btn-danger">
        <a href="../"><input type="button" value="<?= lang("tim_lang.cancel") ?>" class="btn btn-link"></a>
        <input type="hidden" name="userId" value="<?= $userId ?>">
        <input type="hidden" name="ciUserId" value="<?= $ciUserId ?>">
    </form>

</div>