<div class="container py-1">
    <ul class='nav nav-pills'>
        <?php foreach ($buttons as $i => $button) : ?>
            <li class='nav-item'>
            <a href="<?= esc($button['link']) ?>" class='nav-link 
            <?php 
            $uriSegments = explode('/', $button['link']);
            $lastSegment = array_pop($uriSegments);

            if (filter_var($lastSegment, FILTER_VALIDATE_INT) !== false) {
                $lastSegment = array_pop($uriSegments);
            }

            if (url_is("*$lastSegment*") && $i > 2) {
                echo 'active';
            } else if (($button['label'] == ucfirst(lang('tim_lang.siteData'))) && ($isFakeLog)) {
                echo 'active';
            }
            ?>'>
                <?= esc($button['label']) ?>
            </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
