<?php if (isset($date)) : ?>
    <div class="container">
        <input type='date' value='<?= $date ?>'>
    </div>
    <script>
        let date = document.getElementsByTagName('input');
        date = date[0];
        date.onchange = function () {
            window.location = date.value;
        }
    </script>
<?php endif ?>