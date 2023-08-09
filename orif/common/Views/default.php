<?php
/**
 * Default view
 *
 * @author      Orif
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?= view('Common\Views\head_content', $this->data) ?>
    <?= $this->renderSection('head') ?>
</head>
<body>
    <?php if (ENVIRONMENT != 'production'): ?>
        <div class="alert alert-warning text-center">CodeIgniter environment variable is set to
        <?=esc(strtoupper(ENVIRONMENT))?> You can change it in .env file.</div>
    <?php endif ?>
    <?= view('Common\Views\login_bar', $this->data) ?>
    <?php foreach (config('Common\Config\AdminPanelConfig')->tabs as $tab) {
            if (strstr(current_url(),$tab['pageLink'])) {
                echo view('\Common\adminMenu');
            }
        }
    ?>
    <?= $this->renderSection('content') ?>
<?= $this->renderSection('javascript') ?>
</body>
</html>
