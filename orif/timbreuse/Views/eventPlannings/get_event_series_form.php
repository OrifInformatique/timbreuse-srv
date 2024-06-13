<script>
    let createSeriesForm = document.getElementById("create_series_form");
    let createSeriesButton = document.getElementById("create_series");
    const eventSeriesURL = '<?= base_url('event-series/html/form') ?>';

    createSeriesButton.onclick = () => {
        createSeriesButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + createSeriesButton.innerText;
        $.ajax({
            url: eventSeriesURL,
            type: 'get',
            success: (response) => {
                const html = JSON.parse(response);
                createSeriesButton.hidden = true;
                $('#create_series_form').append(html);
            }
        });
    };
</script>