<?php if (isset($date)) : ?>
    <div class="container">
        <input type='date' value='<?= $date ?>'>
    </div>
    <script>
        function redirection() {
            let oDate = new Date(date.value);
            if ((oDate.getTime() === oDate.getTime()) && (oDate.getFullYear() >= 1948)) {
                window.location = '../' + date.value + '/' + '<?= $period ?>';
            }
        }
        let date = document.getElementsByTagName('input');
        date = date[0];
        date.onchange = function () {
            setTimeout(redirection, 500);
            
        }
    </script>
<?php endif ?>