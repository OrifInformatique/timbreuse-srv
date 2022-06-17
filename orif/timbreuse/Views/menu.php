<div class="container">
    <?php foreach ($buttons as $button): ?>
        <a href="<?= $button['link'] ?>" class='btn btn-primary'>
            <?= $button['label'] ?>
        </a>
    <?php endforeach ?>
</div>