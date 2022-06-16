<div class="container">
    <?php foreach ($buttons as $button): ?>
        <a href="<?= $button['link'] ?>">
            <button type='button' class='btn btn-primary'><?= $button['label'] ?></button>
        </a>
    <?php endforeach ?>
</div>