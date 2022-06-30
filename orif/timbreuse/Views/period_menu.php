<div class="container">
    <ul class='nav nav-pills'>
        <?php foreach ($buttons as $button) : ?>
            <li class='nav-item'>
            <a href="<?= $button['link'] ?>" class='nav-link <?= $button['link'] == $period ? 'active': ''?>'>
                <?= $button['label'] ?>
            </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>