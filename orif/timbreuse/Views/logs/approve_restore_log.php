<!-- 
    approve_view 
    it use in restore_log, approve_restore_log
    it takes :
    text
    link
    label_buttuon
    cancel_link
    id
-->
<div class="container">

    <p><?= esc($text) ?></p>
    <form action=<?= esc($link) ?> method="post">
        <?= csrf_field() ?>
        <!-- CSRF protection -->
        <input type="submit" value="<?= esc(ucfirst($label_button)) ?>" class="btn btn-danger">
        <a href="<?= esc($cancel_link) ?>"><input type="button" value="<?= esc(ucfirst(lang("common_lang.btn_cancel"))) ?>" class="btn btn-secondary"></a>
        <input type="hidden" name="id" value="<?= esc($id) ?>">
    </form>

</div>
