<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Copied from Bootstrap model https://getbootstrap.com/docs/4.6/getting-started/introduction/) -->

<title><?php
    if (!isset($title) || is_null($title) || $title == '') {
        echo lang('common_lang.page_prefix');
    } else {
        echo lang('common_lang.page_prefix').' - '.$title;
    }
?></title>
<!-- Icon -->
<link rel="icon" type="image/png" href="<?= base_url("images/favicon.png"); ?>">
<link rel="shortcut icon" type="image/png" href="<?= base_url("images/favicon.png"); ?>">

<!-- Bootstrap  -->
<!-- Orif Bootstrap CSS personalized with https://bootstrap.build/app -->
<link rel="stylesheet" href="<?= base_url("css/bootstrap.min.css"); ?>">
<!-- Bootstrap icons -->
<link rel="stylesheet" href="<?= base_url("css/bootstrap-icons-1.10.5/font/bootstrap-icons.min.css"); ?>">
<!-- jquery, popper and Bootstrap javascript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>


<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Application styles -->
<link rel="stylesheet" href="<?= base_url("css/MY_styles.css"); ?>">
