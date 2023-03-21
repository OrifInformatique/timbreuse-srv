<div class="container">
    <?php foreach ($buttons as $button): ?>
        <a href="<?= esc($button['link']) ?>" class='btn btn-primary'>
            <?= esc($button['label']) ?>
        </a>
    <?php endforeach ?>
</div>
