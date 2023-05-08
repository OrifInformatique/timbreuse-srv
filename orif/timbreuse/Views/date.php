<?php if (isset($date)) : ?>
    <div class="container py-1">
            <input type='date' class='form-control' value='<?= esc($date) ?>' min="1948-04-17">
    </div>
    <script>
        function redirection() {
            let oDate = new Date(date.value);
            if ((oDate.getTime() === oDate.getTime()) && (oDate.getFullYear() >= 1948)) {
                window.location = '../' + date.value + '/' + '<?= esc($period) ?>';
            }
        }
        let date = document.getElementsByTagName('input');
        date = date[0];
        //date.onchange = function () {
        date.onfocusout = function () {
            redirection();
            //setTimeout(redirection, 500);
            
        }
    </script>
<?php endif ?>
