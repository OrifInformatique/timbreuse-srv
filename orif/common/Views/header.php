<?php
/**
 * Header view
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?= view('Common\Views\head_content', $this->data) ?>
</head>
<body>
    <?php if (ENVIRONMENT != 'production'): ?>
        <div class="alert alert-warning text-center">CodeIgniter environment variable is set
        to <?=esc(strtoupper(ENVIRONMENT))?> You can change it in .env file.</div>
    <?php endif ?>
