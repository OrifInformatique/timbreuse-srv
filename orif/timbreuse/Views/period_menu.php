<div class="container py-1">
    <ul class='nav nav-pills'>
        <?php foreach ($buttons as $button) : ?>
            <li class='nav-item'>
            <a href="<?= esc($button['link']) ?>" class='nav-link 
            <?php 
            if ($button['link'] == $period) {
                echo 'active';
            } else if (($button['label'] == ucfirst(lang('tim_lang.siteData'))) && ($isFakeLog)) {
                echo 'active';
            }
            ?>'>
                <?= $button['label'] ?>
            </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
